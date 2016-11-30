<?php
/**
 * Permission.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Security
 * @since          1.0.0
 *
 * @date           10.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Security;

use Nette;
use Nette\Reflection;
use Nette\Security as NS;
use Nette\Utils;

use IPub;
use IPub\Permissions\Access;
use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;
use IPub\Permissions\Providers;

/**
 * Nette user permission
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class Permission extends NS\Permission implements NS\IAuthorizator
{
	/**
	 * @param Providers\IRolesProvider $rolesProvider
	 * @param Providers\IResourcesProvider $resourcesProvider
	 */
	public function __construct(
		Providers\IRolesProvider $rolesProvider,
		Providers\IResourcesProvider $resourcesProvider
	) {
		// Get all available resources
		$resources = $resourcesProvider->findAll();

		foreach ($resources as $resource) {
			$resourceParent = $resource->getParent();

			// Assign resource to application permission checker
			$this->addResource($resource->getResourceId(), $resourceParent ? $resourceParent->getResourceId() : NULL);
		}

		// Get all available roles
		$roles = $rolesProvider->findAll();

		// Register all available roles
		foreach ($roles as $role) {
			$roleParent = $role->getParent();

			// Assign role to application permission checker
			$this->addRole($role->getRoleId(), $roleParent ? $roleParent->getRoleId() : NULL);

			// Allow all privileges for administrator
			if ($role->isAdministrator()) {
				$this->allow($role->getRoleId(), self::ALL, self::ALL);

			// For others apply setup privileges
			} else {
				foreach ($role->getPermissions() as $permission) {
					$resource = $permission->getResource();
					$resource = $resource ? $resource->getResourceId() : NS\IAuthorizator::ALL;

					$this->allow($role->getRoleId(), $resource, $permission->getPrivilege(), $permission->getAssertion());
				}
			}
		}
	}
}
