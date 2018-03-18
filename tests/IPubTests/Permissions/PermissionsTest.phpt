<?php
/**
 * Test: IPub\Permissions\Permissions
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           14.01.15
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

require __DIR__ . DS . 'libs' . DS . 'DummyExtensionOne.php';
require __DIR__ . DS . 'libs' . DS . 'DummyExtensionTwo.php';
require __DIR__ . DS . 'libs' . DS . 'DummyExtensionThree.php';

class PermissionsTest extends Tester\TestCase
{
	/**
	 * @return array[]|array
	 */
	public function dataExtensionsWithInvalidPermissions() : array
	{
		return [
			['DummyExtensionTwo'],
			['DummyExtensionThree'],
		];
	}

	public function testRegisteringPermissions() : void
	{
		$config = $this->initializeContainer();

		\IPubTests\Permissions\Libs\DummyExtensionOne::register($config);

		$dic = $config->createContainer();

		/** @var Permissions\Providers\IPermissionsProvider $permissions */
		$permissionsProvider = $dic->getByType(Permissions\Providers\IPermissionsProvider::class);
		/** @var Permissions\Providers\IResourcesProvider $resourcesProvider */
		$resourcesProvider = $dic->getByType(Permissions\Providers\IResourcesProvider::class);

		Assert::count(6, $permissionsProvider->findAll());
		Assert::count(6, $resourcesProvider->findAll());
	}

	/**
	 * @dataProvider dataExtensionsWithInvalidPermissions
	 *
	 * @param string $extensionName
	 *
	 * @throws IPub\Permissions\Exceptions\InvalidArgumentException
	 */
	public function testRegisteringInvalidPermissions(string $extensionName) : void
	{
		$config = $this->initializeContainer();

		eval('\IPubTests\Permissions\Libs\\' . $extensionName . '::register($config);');

		$config->createContainer();
	}

	/**
	 * @return Nette\Configurator
	 */
	private function initializeContainer() : Nette\Configurator
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		Permissions\DI\PermissionsExtension::register($config);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');

		return $config;
	}
}

\run(new PermissionsTest());
