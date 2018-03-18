<?php
/**
 * Test: IPub\Permissions\Extension
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           13.01.15
 */

declare(strict_types = 1);

namespace IPubTests\Permissions;

use Nette;

use Tester;
use Tester\Assert;

use IPub\Permissions;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require __DIR__ . DS . 'libs' . DS . 'ResourcesProvider.php';
require __DIR__ . DS . 'libs' . DS . 'PermissionsProvider.php';
require __DIR__ . DS . 'libs' . DS . 'RolesProvider.php';

class ExtensionTest extends Tester\TestCase
{
	public function testFunctional() : void
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('permissions.permissions') instanceof Permissions\Security\Permission);
		Assert::true($dic->getService('permissions.config') instanceof Permissions\Configuration);

		Assert::true($dic->getService('permissions.providers.permissions') instanceof Permissions\Providers\IPermissionsProvider);

		Assert::true($dic->getService('permissions.checkers.annotation') instanceof Permissions\Access\AnnotationChecker);
		Assert::true($dic->getService('permissions.checkers.latte') instanceof Permissions\Access\LatteChecker);
		Assert::true($dic->getService('permissions.checkers.link') instanceof Permissions\Access\LinkChecker);
	}

	/**
	 * @return Nette\DI\Container
	 */
	private function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		Permissions\DI\PermissionsExtension::register($config);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');

		return $config->createContainer();
	}
}

\run(new ExtensionTest());
