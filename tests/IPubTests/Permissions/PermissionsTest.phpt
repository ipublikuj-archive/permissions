<?php
/**
 * Test: IPub\Permissions\Permissions
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		14.01.15
 */

namespace IPubTests\Permissions;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\Permissions;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/RolesModel.php';

class PermissionsTest extends Tester\TestCase
{
	/**
	 * @var Permissions\Providers\IRolesProvider
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
			['firstResourceName:firstPrivilegeName', [
				'title'			=> 'This is example title',
				'description'	=> 'This is example description'
			]],
			[(new Permissions\Entities\Permission('secondResource', 'secondPrivilege', [
					'title'			=> 'This is second example title',
					'description'	=> 'This is second example description'
				])), NULL
			],
			[
				[
					'resource'	=> 'thirdResourceName',
					'privilege'	=> 'thirdPrivilegeName'
				]
			]
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidPermissions()
	{
		return [
			['wrongStringVersion', [
				'title'			=> 'This is example title',
				'description'	=> 'This is example description'
			]],
			[
				[
					'resource'	=> 'thirdResourceName',
					'wrongKey'	=> 'thirdPrivilegeName'
				]
			]
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
	}

	/**
	 * @dataProvider dataValidPermissions
	 *
	 * @param mixed|NULL $permission
	 * @param array|NULL $details
	 */
	public function testRegisteringPermissions($permission, array $details = NULL)
	{
		$obj = $this->permission->addPermission($permission, $details);

		Assert::true($obj instanceof IPub\Permissions\Security\Permission);
	}

	/**
	 * @dataProvider dataInvalidPermissions
	 *
	 * @param mixed|NULL $permission
	 * @param array|NULL $details
	 *
	 * @throws IPub\Permissions\Exceptions\InvalidArgumentException
	 */
	public function testRegisteringInvalidPermissions($permission, array $details = NULL)
	{
		$this->permission->addPermission($permission, $details);
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

\run(new PermissionsTest());
