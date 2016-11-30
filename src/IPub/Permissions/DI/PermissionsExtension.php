<?php
/**
 * PermissionsExtension.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           10.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;
use Nette\Utils;

use IPub;
use IPub\Permissions;
use IPub\Permissions\Access;
use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;
use IPub\Permissions\Providers;
use IPub\Permissions\Security;

/**
 * Permission extension container
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class PermissionsExtension extends DI\CompilerExtension
{
	/**
	 * @var array
	 */
	private $defaults = [
		'annotation'  => TRUE,
		'redirectUrl' => NULL,
		'providers'   => [
			'roles'       => TRUE,
			'resources'   => TRUE,
			'permissions' => TRUE,
		],
	];

	public function loadConfiguration()
	{
		// Get container builder
		$builder = $this->getContainerBuilder();
		// Get extension configuration
		$configuration = $this->getConfig($this->defaults);

		// Application permissions
		$builder->addDefinition($this->prefix('permissions'))
			->setClass(Security\Permission::class);

		$builder->addDefinition($this->prefix('config'))
			->setClass(Permissions\Configuration::class, [
				$configuration['redirectUrl'],
			]);

		/**
		 * Data providers
		 */

		if ($configuration['providers']['roles'] === TRUE) {
			$builder->addDefinition($this->prefix('providers.roles'))
				->setClass(Providers\RolesProvider::class);

		} elseif (is_string($configuration['providers']['roles']) && class_exists($configuration['providers']['roles'])) {
			$builder->addDefinition($this->prefix('providers.roles'))
				->setClass($configuration['providers']['roles']);
		}

		if ($configuration['providers']['resources'] === TRUE) {
			$builder->addDefinition($this->prefix('providers.resources'))
				->setClass(Providers\ResourcesProvider::class);

		} elseif (is_string($configuration['providers']['resources']) && class_exists($configuration['providers']['resources'])) {
			$builder->addDefinition($this->prefix('providers.resources'))
				->setClass($configuration['providers']['resources']);
		}

		if ($configuration['providers']['permissions'] === TRUE) {
			$builder->addDefinition($this->prefix('providers.permissions'))
				->setClass(Providers\PermissionsProvider::class);

		} elseif (is_string($configuration['providers']['permissions']) && class_exists($configuration['providers']['permissions'])) {
			$builder->addDefinition($this->prefix('providers.permissions'))
				->setClass($configuration['providers']['permissions']);
		}

		/**
		 * Access checkers
		 */

		// Check if annotation checker is enabled
		if ($configuration['annotation'] === TRUE) {
			// Annotation access checkers
			$builder->addDefinition($this->prefix('checkers.annotation'))
				->setClass(Access\AnnotationChecker::class);
		}

		// Latte access checker
		$builder->addDefinition($this->prefix('checkers.latte'))
			->setClass(Access\LatteChecker::class);

		// Link access checker
		$builder->addDefinition($this->prefix('checkers.link'))
			->setClass(Access\LinkChecker::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile()
	{
		parent::beforeCompile();

		// Get container builder
		$builder = $this->getContainerBuilder();

		// Get acl permissions service
		$permissionsProvider = $builder->findByType(Providers\IPermissionsProvider::class);
		$permissionsProvider = reset($permissionsProvider);

		// Get acl resources service
		$resourcesProvider = $builder->findByType(Providers\IResourcesProvider::class);
		$resourcesProvider = reset($resourcesProvider);

		// Check all extensions and search for permissions provider
		foreach ($this->compiler->getExtensions() as $extension) {
			if (!$extension instanceof IPermissionsProvider) {
				continue;
			}

			// Get permissions & details
			$this->registerPermissionsResources($extension->getPermissions(), $resourcesProvider, $permissionsProvider);
		}

		// Install extension latte macros
		$latteFactory = $builder->getDefinition($builder->getByType(Nette\Bridges\ApplicationLatte\ILatteFactory::class) ?: 'nette.latteFactory');

		$latteFactory
			->addSetup('IPub\Permissions\Latte\Macros::install(?->getCompiler())', ['@self']);
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 * 
	 * @return void
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'permissions')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new PermissionsExtension());
		};
	}

	/**
	 * @param array $permissions
	 * @param DI\ServiceDefinition|NULL $resourcesProvider
	 * @param DI\ServiceDefinition|NULL $permissionsProvider
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	private function registerPermissionsResources(
		array $permissions,
		DI\ServiceDefinition $resourcesProvider = NULL,
		DI\ServiceDefinition $permissionsProvider = NULL
	) {
		foreach ($permissions as $permission => $details) {
			if (is_array($permission)) {
				if (!isset($permission['resource']) || !isset($permission['privilege'])) {
					throw new Exceptions\InvalidArgumentException('Permission must include resource & privilege.');
				}

				// Remove white spaces
				$resource = Utils\Strings::trim($permission['resource']);
				$privilege = Utils\Strings::trim($permission['privilege']);

				$resource = new Entities\Resource($resource);

			} elseif ($permission instanceof Entities\IPermission) {
				$resource = $permission->getResource();
				$privilege = $permission->getPrivilege();

			// Resource & privilege is in string with delimiter
			} elseif (is_string($permission) && Utils\Strings::contains($permission, Entities\IPermission::DELIMITER)) {
				// Parse resource & privilege from permission
				list($resource, $privilege) = explode(Entities\IPermission::DELIMITER, $permission);

				// Remove white spaces
				$resource = Utils\Strings::trim($resource);
				$privilege = Utils\Strings::trim($privilege);

				$resource = new Entities\Resource($resource);

			} else {
				throw new Exceptions\InvalidArgumentException(sprintf('Permission must be only string with delimiter, array with resource & privilege or instance of IPub\Permissions\Entities\IPermission, %s given', gettype($permission)));
			}

			// Assign permission to service
			$permissionsProvider->addSetup('addPermission', [$resource, $privilege, $details]);
			$resourcesProvider->addSetup('addResource', [$resource->getResourceId()]);
		}
	}
}
