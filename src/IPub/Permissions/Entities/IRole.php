<?php
/**
 * IRole.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           12.03.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Entities;

use Nette\Security as NS;

interface IRole extends NS\IRole
{
	/**
	 * The identifier of the anonymous role
	 *
	 * @var string
	 */
	public const ROLE_ANONYMOUS = 'guest';

	/**
	 * The identifier of the authenticated role
	 *
	 * @var string
	 */
	public const ROLE_AUTHENTICATED = 'authenticated';

	/**
	 * The identifier of the administrator role
	 *
	 * @var string
	 */
	public const ROLE_ADMINISTRATOR = 'administrator';

	/**
	 * @param IRole|NULL $parent
	 *
	 * @return void
	 */
	function setParent(?IRole $parent = NULL) : void;

	/**
	 * @return IRole|NULL
	 */
	function getParent() : ?IRole;

	/**
	 * @param IRole[] $roles
	 *
	 * @return void
	 */
	function setChildren(array $roles) : void;

	/**
	 * @param IRole $role
	 *
	 * @return void
	 */
	function addChild(IRole $role) : void;

	/**
	 * @return IRole[]
	 */
	function getChildren() : array;

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	function setName(string $name) : void;

	/**
	 * @return string|NULL
	 */
	function getName() : ?string;

	/**
	 * @param string $comment
	 *
	 * @return void
	 */
	function setComment(string $comment) : void;

	/**
	 * @return string|NULL
	 */
	function getComment() : ?string;

	/**
	 * Add one permission to role
	 *
	 * @param IPermission[] $permissions
	 *
	 * @return void
	 */
	function setPermissions(array $permissions) : void;

	/**
	 * Adds a permission
	 *
	 * @param IPermission $permission
	 *
	 * @return void
	 */
	function addPermission(IPermission $permission) : void;

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
	function removePermission(IPermission $permission) : void;

	/**
	 * Clear all role permissions
	 *
	 * @return void
	 */
	function clearPermissions() : void;

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
