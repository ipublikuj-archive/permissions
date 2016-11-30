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

use IPub;
use IPub\Permissions;
use IPub\Permissions\Entities;

class RolesProvider extends Permissions\Providers\RolesProvider
{
	public function __construct()
	{
		// Create guest role
		$guest = $this->addRole(Entities\IRole::ROLE_ANONYMOUS);
		$guest->setName('Guest');
		$guest->setPermissions([
			'firstResourceName:firstPrivilegeName'
		]);

		// Create authenticated role
		$authenticated = new Permissions\Entities\Role(Entities\IRole::ROLE_AUTHENTICATED, 'Registered user');
		$authenticated->setPermissions([
			'firstResourceName:firstPrivilegeName',
			'secondResourceName:secondPrivilegeName'
		]);

		$administrator = new Permissions\Entities\Role(Entities\IRole::ROLE_ADMINISTRATOR, 'Administrator');
		$administrator->setPermissions([
			'firstResourceName:firstPrivilegeName',
			'secondResourceName:secondPrivilegeName',
			'thirdResourceName:thirdPrivilegeName'
		]);

		$customChild = new Permissions\Entities\Role('user-defined-child-role', 'User Defined Child', 'Registered in custom role as children of another role');
		$customChild->setPermissions([
		]);

		$custom = new Permissions\Entities\Role('user-defined-role', 'User Defined', 'Registered in custom role');
		$custom->setChildren([$customChild]);
		$custom->setPermissions([
			'firstResourceName:firstPrivilegeName',
			'thirdResourceName:thirdPrivilegeName'
		]);

		$customInherited = new Permissions\Entities\Role('user-defined-inherited-role', 'User Defined Inherited', 'Registered in custom role inheriting another role');
		$customInherited->setParent($custom);
		$customInherited->setPermissions([
		]);
	}
}
