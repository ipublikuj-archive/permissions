<?php
/**
 * IRole.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           12.03.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Entities;

use Nette;
use Nette\Security as NS;

interface IRole extends NS\IRole
{
	/**
	 * The identifier of the anonymous role
	 *
	 * @var string
	 */
	const ROLE_ANONYMOUS = 'guest';

	/**
	 * The identifier of the authenticated role
	 *
	 * @var string
	 */
	const ROLE_AUTHENTICATED = 'authenticated';

	/**
	 * The identifier of the administrator role
	 *
	 * @var string
	 */
	const ROLE_ADMINISTRATOR = 'administrator';

	/**
	 * @param IRole $parent
	 *
	 * @return void
	 */
	function setParent(IRole $parent = NULL);

	/**
	 * @return IRole
	 */
	function getParent();

	/**
	 * @param IRole[] $roles
	 *
	 * @return void
	 */
	function setChildren(array $roles);

	/**
	 * @param IRole $role
	 *
	 * @return void
	 */
	function addChild(IRole $role);

	/**
	 * @return IRole[]
	 */
	function getChildren();

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	function setName(string $name);

	/**
	 * @return string|NULL
	 */
	function getName();

	/**
	 * @param string $comment
	 *
	 * @return void
	 */
	function setComment(string $comment);

	/**
	 * @return string|NULL
	 */
	function getComment();

	/**
	 * Add one permission to role
	 *
	 * @param IPermission[] $permissions
	 *
	 * @return void
	 */
	function setPermissions(array $permissions);

	/**
	 * Adds a permission
	 *
	 * @param IPermission $permission
	 *
	 * @return void
	 */
	function addPermission(IPermission $permission);

	/**
	 * Returns permissions for the role
	 *
	 * @return IPermission[]
	 */
	function getPermissions() : array;

	/**
	 * Checks if a permission exists for this role.
	 *
	 * @param  IPermission $permission
	 *
	 * @return bool
	 */
	function hasPermission(IPermission $permission) : bool;

	/**
	 * Remove one specific permission from the role
	 *
	 * @param IPermission $permission
	 *
	 * @return void
	 */
	function removePermission(IPermission $permission);

	/**
	 * Clear all role permissions
	 *
	 * @return void
	 */
	function clearPermissions();

	/**
	 * Check if role is one from system roles
	 *
	 * @return bool
	 */
	function isLocked() : bool;

	/**
	 * Check if role is guest
	 *
	 * @return bool
	 */
	function isAnonymous() : bool;

	/**
	 * Check if role is authenticated
	 *
	 * @return bool
	 */
	function isAuthenticated() : bool;

	/**
	 * Check if role is administrator
	 *
	 * @return bool
	 */
	function isAdministrator() : bool;
}
