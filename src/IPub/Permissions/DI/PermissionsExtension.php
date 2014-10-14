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
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\PhpGenerator as Code;

use IPub;

if (!class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
	class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
	class_alias('Nette\Config\Helpers', 'Nette\DI\Config\Helpers');
}

if (isset(Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']) || !class_exists('Nette\Configurator')) {
	unset(Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']);
	class_alias('Nette\Config\Configurator', 'Nette\Configurator');
}

class PermissionsExtension extends Nette\DI\CompilerExtension
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
		$config->onCompile[] = function (Configurator $config, Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new PermissionsExtension());
		};
	}
}