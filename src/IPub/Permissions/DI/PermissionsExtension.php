<?php
/**
 * PermissionsExtension.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		10.10.14
 */

namespace IPub\Permissions\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;

class PermissionsExtension extends DI\CompilerExtension
{
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		// Application permissions
		$builder->addDefinition($this->prefix('permissions'))
			->setClass('IPub\Permissions\Security\Permission');

		// Annotation access checkers
		$builder->addDefinition($this->prefix('checkers.annotation'))
			->setClass('IPub\Permissions\Access\AnnotationChecker');

		// Latte access checker
		$builder->addDefinition($this->prefix('checkers.latte'))
			->setClass('IPub\Permissions\Access\LatteChecker');

		// Link access checker
		$builder->addDefinition($this->prefix('checkers.link'))
			->setClass('IPub\Permissions\Access\LinkChecker');

		// Install extension latte macros
		$latteFactory = $builder->hasDefinition('nette.latteFactory')
			? $builder->getDefinition('nette.latteFactory')
			: $builder->getDefinition('nette.latte');

		$latteFactory
			->addSetup('IPub\Permissions\Latte\Macros::install(?->getCompiler())', array('@self'));
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		// Get acl permissions service
		$service = $builder->getDefinition($this->prefix('permissions'));

		// Check all extensions and search for permissions provider
		foreach ($this->compiler->getExtensions() as $extension) {
			if (!$extension instanceof IPermissionsProvider) {
				continue;
			}

			// Get permissions & details
			foreach($extension->getPermissions() as $permission => $details) {
				// Assign permission to service
				$service->addSetup('addPermission', array($permission, $details));
			}
		}
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'permissions')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new PermissionsExtension());
		};
	}
}