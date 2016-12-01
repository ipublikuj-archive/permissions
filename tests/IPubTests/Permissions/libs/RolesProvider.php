<?php
/**
 * Test: IPub\Permissions\Libraries
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

namespace IPubTests\Permissions\Libs;

use Nette;

use IPub;
use IPub\Permissions;
use IPub\Permissions\Entities;
use IPub\Permissions\Providers;

class RolesProvider extends Providers\RolesProvider
{
	public function __construct(PermissionsProvider $permissionsProvider)
	{
		// Create guest role
		$guest = $this->addRole(Entities\IRole::ROLE_ANONYMOUS, NULL, $permissionsProvider->getPermission('firstResource:firstPrivilege'));
		$guest->setName('Guest');

		// Create authenticated role
		$authenticated = $this->addRole(Entities\IRole::ROLE_AUTHENTICATED, $guest, [
			$permissionsProvider->getPermission('firstResource:firstPrivilege'),
			$permissionsProvider->getPermission('secondResource:secondPrivilege'),
		]);
		$authenticated->setName('Registered user');

		$administrator = $this->addRole(Entities\IRole::ROLE_ADMINISTRATOR, NULL, [
			$permissionsProvider->getPermission('firstResource:firstPrivilege'),
			$permissionsProvider->getPermission('secondResource:secondPrivilege'),
			$permissionsProvider->getPermission('thirdResource:thirdPrivilege'),
		]);
		$administrator->setName('Administrator');

		$customChild = $this->addRole('user-defined-child-role');
		$customChild->setName('User Defined Child');
		$customChild->setComment('Registered in custom role as children of another role');

		$custom = $this->addRole('user-defined-role', NULL, [
			$permissionsProvider->getPermission('firstResource:firstPrivilege'),
			$permissionsProvider->getPermission('thirdResource:thirdPrivilege'),
		]);
		$custom->addChild($customChild);
		$custom->setName('User Defined');
		$custom->setComment('Registered in custom role');

		$customInherited = $this->addRole('user-defined-inherited-role');
		$customInherited->setParent($custom);
		$customInherited->setName('User Defined Inherited');
		$customInherited->setComment('Registered in custom role inheriting another role');
	}
}
