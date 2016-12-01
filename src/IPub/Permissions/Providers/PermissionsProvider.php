<?php
/**
 * PermissionsProvider.php
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
use Nette\Utils;

use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;

/**
 * Basic permissions provider
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class PermissionsProvider extends Nette\Object implements IPermissionsProvider
{
	/**
	 * @var Entities\IPermission[]
	 */
	private $permissions = [];

	/**
	 * @param Entities\IResource|NULL $resource
	 * @param string|NULL $privilege
	 * @param array|NULL $details
	 * @param callable|NULL $assertion
	 *
	 * @return Entities\IPermission
	 */
	public function addPermission(Entities\IResource $resource = NULL, string $privilege = NULL, array $details = NULL, callable $assertion = NULL) : Entities\IPermission
	{
		$permission = new Entities\Permission($resource, $privilege, $details, $assertion);

		if (array_key_exists((string) $permission, $this->permissions)) {
			throw new Exceptions\InvalidStateException(sprintf('Permission "%s" is already registered.', (string) $permission));
		}

		$this->permissions[(string) $permission] = $permission;

		return $permission;
	}

	/**
	 * @param string $id
	 *
	 * @return Entities\IPermission
	 */
	public function getPermission(string $id) : Entities\IPermission
	{
		if (!array_key_exists($id, $this->permissions)) {
			throw new Exceptions\InvalidStateException(sprintf('Permission "%s" is not registered.', $id));
		}

		return $this->permissions[$id];
	}

	/**
	 * @return Entities\IPermission[]
	 */
	public function findAll() : array
	{
		return $this->permissions;
	}
}
