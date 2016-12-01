<?php
/**
 * Test: IPub\Permissions\ResourcesInheritance
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Igor Hlina http://www.srigi.sk
 * @package        iPublikuj:Permissions!
 * @subpackage     Tests
 * @since          2.0.0
 *
 * @date           23.07.15
 */

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

class ResourcesInheritanceTest extends Tester\TestCase
{
	/**
	 * @var Permissions\Security\Permission
	 */
	private $permission;


	/**
	 * Set up
	 */
	public function setUp()
	{
		parent::setUp();

		$dic = $this->createContainer();

		// Get permissions service
		$this->permission = $dic->getService('permissions.permissions');
	}


	public function testResourcePermissionsInheriting()
	{
		Assert::true($this->permission->isAllowed('authenticated', 'firstResource', 'firstPrivilege'),
			'Role "authenticated" can access "firstResource:firstPrivilege"');

		Assert::false($this->permission->isAllowed('authenticated', 'firstResource'),
			'Role "authenticated" can not access all privileges from "firstResource:" resource');

		Assert::false($this->permission->isAllowed('authenticated', 'thirdResource', 'thirdPrivilege'),
			'Role "authenticated" can not access "thirdResource:thirdPrivilege"');

		Assert::true($this->permission->isAllowed('user-defined-child-role', 'firstResource', 'firstPrivilege'),
			'Role "user-defined-child-role" can access "firstResource:firstPrivilege"');

		Assert::true($this->permission->isAllowed('user-defined-role', 'firstResource', 'firstPrivilege'),
			'Role "user-defined-role" can access "firstResource:firstPrivilege"');

		Assert::true($this->permission->isAllowed('user-defined-inherited-role', 'firstResource', 'firstPrivilege'),
			'Role "user-defined-inherited-role" can access "firstResource:firstPrivilege"');
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

\run(new ResourcesInheritanceTest());
