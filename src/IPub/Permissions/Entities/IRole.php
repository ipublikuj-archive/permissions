<?php
/**
 * IRole.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Entities
 * @since		5.0
 *
 * @date		12.03.14
 */

namespace IPub\Permissions\Entities;

interface IRole
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
	 * @return $this
	 */
	public function setParent(IRole $parent = NULL);

	/**
	 * @return IRole
	 */
	public function getParent();

	/**
	 * @param array $roles
	 *
	 * @return $this
	 */
	public function setChildren($roles);

	/**
	 * @return array
	 */
	public function getChildren();

	/**
	 * @param string $keyName
	 *
	 * @return $this
	 */
	public function setKeyName($keyName);

	/**
	 * @return string
	 */
	public function getKeyName();

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $comment
	 *
	 * @return $this
	 */
	public function setComment($comment);

	/**
	 * @return string
	 */
	public function getComment();

	/**
	 * @param int $priority
	 *
	 * @return $this
	 */
	public function setPriority($priority);

	/**
	 * @return int
	 */
	public function getPriority();

	/**
	 * Returns permissions for the role.
	 *
	 * @return string[]
	 */
	public function getPermissions();

	/**
	 * Add one permission to role
	 *
	 * @param string $permissions
	 *
	 * @return $this
	 */
	public function setPermissions($permissions);

	/**
	 * Checks if a permission exists for this role.
	 *
	 * @param  string $permission
	 *
	 * @return bool
	 */
	public function hasPermission($permission);

	/**
	 * Adds a permission
	 *
	 * @param string $permission
	 */
	public function addPermission($permission);

	/**
	 * Clear all role permissions
	 *
	 * @return $this
	 */
	public function clearPermissions();

	/**
	 * Check if role is one from system roles
	 *
	 * @return bool
	 */
	public function isLocked();

	/**
	 * Check if role is guest
	 *
	 * @return bool
	 */
	public function isAnonymous();

	/**
	 * Check if role is authenticated
	 *
	 * @return bool
	 */
	public function isAuthenticated();

	/**
	 * Check if role is administrator
	 *
	 * @return bool
	 */
	public function isAdministrator();
}