<?php
/**
 * Test: IPub\Permissions\Extension
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
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

use IPub;
use IPub\Permissions;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require __DIR__ . DS . 'libs' . DS . 'ResourcesProvider.php';
require __DIR__ . DS . 'libs' . DS . 'PermissionsProvider.php';
require __DIR__ . DS . 'libs' . DS . 'RolesProvider.php';

class ExtensionTest extends Tester\TestCase
{
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

	public function testFunctional()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('permissions.permissions') instanceof IPub\Permissions\Security\Permission);
		Assert::true($dic->getService('permissions.config') instanceof IPub\Permissions\Configuration);

		Assert::true($dic->getService('permissions.providers.permissions') instanceof IPub\Permissions\Providers\IPermissionsProvider);

		Assert::true($dic->getService('permissions.checkers.annotation') instanceof IPub\Permissions\Access\AnnotationChecker);
		Assert::true($dic->getService('permissions.checkers.latte') instanceof IPub\Permissions\Access\LatteChecker);
		Assert::true($dic->getService('permissions.checkers.link') instanceof IPub\Permissions\Access\LinkChecker);
	}
}

\run(new ExtensionTest());
