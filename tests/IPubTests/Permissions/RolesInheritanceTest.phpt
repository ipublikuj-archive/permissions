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

class RolesInheritanceTest extends Tester\TestCase
{
	/**
	 * @var Permissions\Providers\RolesProvider
	 */
	private $rolesProvider;

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

		// Get permissions services
		$this->permission = $dic->getService('permissions.permissions');
		$this->rolesProvider = $dic->getByType(Permissions\Providers\IRolesProvider::class);
	}


	public function testRolesProviderHierarchy()
	{
		$administrator = $this->rolesProvider->getRole('administrator');

		Assert::null($administrator->getParent(), 'Role "administrator" does not have parent');
		Assert::count(0, $administrator->getChildren(), 'Role "administrator" does not have any children');

		$guest = $this->rolesProvider->getRole('guest');

		Assert::null($guest->getParent(), 'Role "guest" does not have parents');
		Assert::count(1, $guest->getChildren(), 'Role "guest" does have one children');

		$authenticated = $this->rolesProvider->getRole('authenticated');

		Assert::equal($guest, $authenticated->getParent(), 'Parent role of "authenticated" is "guest"');
		Assert::count(0, $authenticated->getChildren(), 'Role "authenticated" does not have any children');

		$userDefined = $this->rolesProvider->getRole('user-defined-role');

		Assert::null($userDefined->getParent(), 'Role "user-defined-role" does not have parents');
		Assert::count(2, $userDefined->getChildren(), 'Role "user-defined-role" does have 2 children');

		$userDefinedChild = $this->rolesProvider->getRole('user-defined-child-role');

		Assert::equal($userDefined, $userDefinedChild->getParent(), 'Parent role of "user-defined-child-role" is "user-defined-child"');
		Assert::count(0, $userDefinedChild->getChildren(), 'Role "user-defined-child-role" does not have any children');

		$userDefinedInherited = $this->rolesProvider->getRole('user-defined-inherited-role');

		Assert::equal($userDefined, $userDefinedInherited->getParent(), 'Parent role of "user-defined-inherited-role" is "user-defined-role"');
		Assert::count(0, $userDefinedInherited->getChildren(), 'Role "user-defined-inherited-role" does not have any children');
	}

	public function testPermissionRolesHierarchy()
	{
		Assert::equal($this->permission->getRoleParents('administrator'), [], 'Role "administrator" does not have parents');

		Assert::equal($this->permission->getRoleParents('guest'), [], 'Role "guest" does not have parents');

		Assert::equal($this->permission->getRoleParents('authenticated'), ['guest'], 'Role "guest" is the only parent role of "authenticated"');

		Assert::equal($this->permission->getRoleParents('user-defined-role'), [], 'Role "user-defined-role" does not have parents');

		Assert::equal($this->permission->getRoleParents('user-defined-child-role'), ['user-defined-role'], 'Role "user-defined-child-role" is the only parent role of "user-defined-role"');

		Assert::equal($this->permission->getRoleParents('user-defined-inherited-role'), ['user-defined-role'], 'Role "user-defined-inherited-role" is the only parent role of "user-defined-role"');

		Assert::true($this->permission->roleInheritsFrom('user-defined-child-role', 'user-defined-role'), 'Role "user-defined-child-role" inherits from "user-defined-role"');
	}


	public function testPermissionInheritance()
	{
		Assert::true($this->permission->isAllowed('user-defined-role', 'firstResource', 'firstPrivilege'),
			'Role "user-defined-role" is allowed "firstResource:"');

		Assert::true($this->permission->isAllowed('user-defined-child-role', 'firstResource', 'firstPrivilege'),
			'Role "user-defined-child-role" is also allowed "firstResource:" because it inherits from "user-defined-role"');
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


\run(new RolesInheritanceTest());
