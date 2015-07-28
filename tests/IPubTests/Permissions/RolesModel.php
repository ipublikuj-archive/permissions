<?php
/**
 * Test: IPub\Permissions\Extension
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		13.01.15
 */

namespace IPubTests\Permissions;

use Nette;

use IPub;
use IPub\Permissions;
use IPub\Permissions\Entities;

class RolesModel implements Permissions\Models\IRolesModel
{
	/**
	 * @return array|Entities\IRole[]
	 */
	public function findAll()
	{
		// Create guest role
		$guest = (new Permissions\Entities\Role)
			->setKeyName(Entities\IRole::ROLE_ANONYMOUS)
			->setName('Guest')
			->setPriority(0)
			->setPermissions([
				'firstResourceName:firstPrivilegeName'
			]);

		// Create authenticated role
		$authenticated = (new Permissions\Entities\Role)
			->setKeyName(Entities\IRole::ROLE_AUTHENTICATED)
			->setName('Registered user')
			->setPriority(0)
			->setPermissions([
				'firstResourceName:firstPrivilegeName',
				'secondResourceName:secondPrivilegeName'
			]);

		$administrator = (new Permissions\Entities\Role)
			->setKeyName(Entities\IRole::ROLE_ADMINISTRATOR)
			->setName('Administrator')
			->setPriority(0)
			->setPermissions([
				'firstResourceName:firstPrivilegeName',
				'secondResourceName:secondPrivilegeName',
				'thirdResourceName:thirdPrivilegeName'
			]);

		$customChild = (new Permissions\Entities\Role)
			->setKeyName('user-defined-child-role')
			->setName('Registered in custom role as children of another role')
			->setPriority(0)
			->setPermissions([
			]);

		$custom = (new Permissions\Entities\Role)
			->setKeyName('user-defined-role')
			->setName('Registered in custom role')
			->setPriority(0)
			->setChildren([$customChild])
			->setPermissions([
				'firstResourceName:firstPrivilegeName',
				'thirdResourceName:thirdPrivilegeName'
			]);

		$customInherited = (new Permissions\Entities\Role)
			->setKeyName('user-defined-inherited-role')
			->setName('Registered in custom role inheriting another role')
			->setPriority(0)
			->setParent($custom)
			->setPermissions([
			]);


		return [
			$guest,
			$authenticated,
			$administrator,
			$customChild,
			$custom,
			$customInherited,
		];
	}
}
