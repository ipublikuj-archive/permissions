<?php
/**
 * RolesProvider.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 * @since          2.0.0
 *
 * @date           30.11.16
 */

declare(strict_types = 1);

namespace IPub\Permissions\Providers;

use Nette;

use IPub;
use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;

/**
 * Basic roles provider
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class RolesProvider implements IRolesProvider
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var Entities\IRole[]
	 */
	private $roles = [];

	/**
	 * @param string $id
	 * @param Entities\IRole|NULL $parent
	 * @param Entities\IPermission|Entities\IPermission[]|NULL $permissions
	 *
	 * @return Entities\IRole
	 */
	public function addRole(string $id, Entities\IRole $parent = NULL, $permissions = NULL) : Entities\IRole
	{
		if (array_key_exists($id, $this->roles)) {
			throw new Exceptions\InvalidStateException(sprintf('Role "%s" has been already added.', $id));
		}

		if ($permissions instanceof Entities\IPermission) {
			$permissions = [$permissions];
		}

		$role = new Entities\Role($id);

		if ($parent) {
			$role->setParent($parent);
		}

		if ($permissions) {
			$role->setPermissions($permissions);
		}

		$this->roles[$id] = $role;

		return $role;
	}

	/**
	 * @param string $roleName
	 *
	 * @return Entities\IRole
	 */
	public function getRole(string $roleName) : Entities\IRole
	{
		if (!array_key_exists($roleName, $this->roles)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Role "%s" is not registered.', $roleName));
		}

		return $this->roles[$roleName];
	}

	/**
	 * @return Entities\IRole[]
	 */
	public function findAll() : array
	{
		return $this->roles;
	}
}
