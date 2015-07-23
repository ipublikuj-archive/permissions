<?php
/**
 * Test: IPub\Permissions\Permissions
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Igor Hlina http://www.srigi.sk
 * @package		iPublikuj:Permissions!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		23.07.15
 */

namespace IPubTests\Permissions;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\Permissions;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/RolesModel.php';

class InheritanceTest extends Tester\TestCase
{
	/**
	 * @var Permissions\Models\IRolesModel
	 */
	private $rolesModel;

	/**
	 * @var Permissions\Security\Permission
	 */
	private $permission;

	/**
	 * @return array[]|array
	 */
	public function dataValidPermissions()
	{
		return [
			['firstResourceName:firstPrivilegeName', []],
			[(new Permissions\Entities\Permission('secondResource', 'secondPrivilege', [])), NULL],
			['thirdResourceName:thirdPrivilegeName', NULL],
		];
	}


	/**
	 * Set up
	 */
	public function setUp()
	{
		parent::setUp();

		$dic = $this->createContainer();

		// Get roles model services
		$this->rolesModel = $dic->getService('models.roles');

		// Get permissions service
		$this->permission = $dic->getService('permissions.permissions');

		foreach ($this->dataValidPermissions() as list($permission, $details)) {
			$this->permission->addPermission($permission, $details);
		}
	}


	public function testPermissionChild()
	{
		Assert::true($this->permission->isAllowed('user-defined-child-role', 'firstResourceName', 'firstPrivilegeName'));
	}


	public function testPermissionInheritance()
	{
		Assert::true($this->permission->isAllowed('user-defined-role', 'firstResourceName', 'firstPrivilegeName'));
		Assert::true($this->permission->isAllowed('user-defined-inherited-role', 'firstResourceName', 'firstPrivilegeName'));
	}


	/**
	 * @return \SystemContainer|\Nette\DI\Container
	 */
	protected function createContainer()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		Permissions\DI\PermissionsExtension::register($config);

		$config->addConfig(__DIR__ . '/files/config.neon', $config::NONE);

		return $config->createContainer();
	}
}

\run(new InheritanceTest());
