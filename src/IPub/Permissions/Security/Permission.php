<?php
/**
 * Permission.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Security
 * @since		5.0
 *
 * @date		10.10.14
 */

namespace IPub\Permissions\Security;

use Nette;
use Nette\Reflection;
use Nette\Security as NS;
use Nette\Utils;

use IPub;
use IPub\Permissions\Access;
use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;
use IPub\Permissions\Models;
use IPub\Permissions\Templating;
use Tracy\Debugger;

class Permission extends NS\Permission implements NS\IAuthorizator
{
	/**
	 * Permission string delimiter
	 *
	 * @var string
	 */
	const DELIMITER = ':';

	/**
	 * @var Entities\IPermission[]
	 */
	protected $permissions = [];

	/**
	 * @var Entities\IRole[]
	 */
	protected $roles = [];

	/**
	 * @param Models\IRolesModel $rolesModel
	 */
	public function __construct(
		Models\IRolesModel $rolesModel
	) {
		// Get all available roles
		$roles = $rolesModel->findAll();

		// Register all available roles
		foreach ($roles as $role) {
			// Assign role to application permission checker
			$parent = $role->getParent();
			$this->addRole($role->getKeyName(), ($parent) ? $parent->getKeyName() : NULL);

			// & store role in object for future use
			$this->roles[$role->getKeyName()] = $role;
		}
	}

	/**
	 * @param string|array $permission
	 * @param array $details
	 *
	 * @return $this
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function addPermission($permission, $details = [])
	{
		if (is_array($permission)) {
			if (!isset($permission['resource']) || !isset($permission['privilege'])) {
				throw new Exceptions\InvalidArgumentException('Permission must include resource & privilege.');
			}

			// Remove white spaces
			$resource	= Utils\Strings::trim($permission['resource']);
			$privilege	= Utils\Strings::trim($permission['privilege']);

			// Convert it to string form
			$permission = $resource . self::DELIMITER . $privilege;

			// Store permission definition in object
			$this->permissions[$permission] = new Entities\Permission($resource, $privilege, $details);

		} else if ($permission instanceof Entities\IPermission) {
			// Extract resource & privilege from permission
			$resource	= $permission->getResource();
			$privilege	= $permission->getPrivilege();

			// Store permission definition in object
			$this->permissions[(string) $permission] = $permission;

		// Resource & privilege is in string with delimiter
		} else if (is_string($permission) && Utils\Strings::contains($permission, self::DELIMITER)) {
			// Parse resource & privilege from permission
			list($resource, $privilege) = explode(self::DELIMITER, $permission);

			// Remove white spaces
			$resource	= Utils\Strings::trim($resource);
			$privilege	= Utils\Strings::trim($privilege);

			// Convert it to string form
			$permission = $resource . self::DELIMITER . $privilege;

			// Store permission definition in object
			$this->permissions[$permission] = new Entities\Permission($resource, $privilege, $details);

		} else {
			throw new Exceptions\InvalidArgumentException('Permission must be only string with delimiter, array with resource & privilege or instance of IPub\Permissions\Entities\IPermission, ' . gettype($permission) . ' given');
		}

		// Check if resource exists...
		if (!$this->hasResource($resource)) {
			// ...& add resource to parent
			$this->addResource($resource);
		}

		foreach ($this->roles as $role) {
			// Allow all privileges for administrator
			if ($role->isAdministrator()) {
				$this->allow($role->getKeyName(), self::ALL, self::ALL);

			// For others apply setup privileges
			} else {
				// This combination role-resource-privilege is allowed
				if ($role->hasPermission($permission)) {
					$this->allow($role->getKeyName(), $resource, $privilege);
				}
			}
		}

		return $this;
	}

	/**
	 * Get all registered permissions
	 *
	 * @return Entities\IPermission[]
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}
}