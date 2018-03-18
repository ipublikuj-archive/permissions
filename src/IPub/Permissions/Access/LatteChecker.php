<?php
/**
 * LatteChecker.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 * @since          1.0.0
 *
 * @date           14.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Access;

use Nette;
use Nette\Utils;
use Nette\Security as NS;

use IPub;
use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;

/**
 * Latte helper for access checking
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class LatteChecker implements IChecker
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var NS\User
	 */
	private $user;

	/**
	 * @param NS\User $user
	 */
	public function __construct(NS\User $user)
	{
		$this->user = $user;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAllowed($element) : bool
	{
		// Check annotations only if element have to be secured
		if (is_array($element)) {
			$element = Utils\ArrayHash::from($element);

			return $this->checkUser($element)
			&& $this->checkResources($element)
			&& $this->checkPrivileges($element)
			&& $this->checkPermission($element)
			&& $this->checkRoles($element);

		} else {
			return TRUE;
		}
	}

	/**
	 * @param Utils\ArrayHash $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	private function checkUser(Utils\ArrayHash $element) : bool
	{
		// Check if element has user parameter
		if ($element->offsetExists('user')) {
			// Get user parameter
			$user = $element->offsetGet('user');

			// Parameter is single string
			if (is_string($user) && in_array($user, ['loggedIn', 'guest'], TRUE)) {
				// User have to be logged in and is not
				if ($user === 'loggedIn' && $this->user->isLoggedIn() === FALSE) {
					return FALSE;

				// User have to be logged out and is logged in
				} elseif ($user === 'guest' && $this->user->isLoggedIn() === TRUE) {
					return FALSE;
				}

			// Parameter have multiple definitions
			} else {
				throw new Exceptions\InvalidArgumentException('In parameter \'user\' is allowed only one from two strings: \'loggedIn\' & \'guest\'');
			}

			return TRUE;
		}

		return TRUE;
	}

	/**
	 * @param Utils\ArrayHash $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	protected function checkResources(Utils\ArrayHash $element) : bool
	{
		// Check if element has resource parameter & privilege parameter
		if ($element->offsetExists('resource')) {
			$resources = (array) $element->offsetGet('resource');
			$privileges = $element->offsetExists('privilege') ? (array) $element->offsetGet('privilege') : [];

			if (count($resources) != 1) {
				throw new Exceptions\InvalidStateException('Invalid resources count in \'resource\' parameter!');
			}

			foreach ($resources as $resource) {
				if (count($privileges)) {
					foreach ($privileges as $privilege) {
						if ($this->user->isAllowed($resource, $privilege)) {
							return TRUE;
						}
					}

				} else {
					if ($this->user->isAllowed($resource)) {
						return TRUE;
					}
				}
			}

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param Utils\ArrayHash $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	protected function checkPrivileges(Utils\ArrayHash $element) : bool
	{
		// Check if element has privilege parameter & hasn't resource parameter
		if (!$element->offsetExists('resource') && $element->offsetExists('privilege')) {
			$privileges = (array) $element->offsetGet('privilege');

			if (count($privileges) != 1) {
				throw new Exceptions\InvalidStateException('Invalid privileges count in \'privilege\' parameter!');
			}

			foreach ($privileges as $privilege) {
				if ($this->user->isAllowed(NS\IAuthorizator::ALL, $privilege)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param Utils\ArrayHash $element
	 *
	 * @return bool
	 */
	protected function checkPermission(Utils\ArrayHash $element) : bool
	{
		// Check if element has permission parameter
		if ($element->offsetExists('permission')) {
			$permissions = (array) $element->offsetGet('permission');

			foreach ($permissions as $permission) {
				// Parse resource & privilege from permission
				list($resource, $privilege) = explode(Entities\IPermission::DELIMITER, $permission);

				// Remove white spaces
				$resource = Utils\Strings::trim($resource);
				$privilege = Utils\Strings::trim($privilege);

				if ($this->user->isAllowed($resource, $privilege)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param Utils\ArrayHash $element
	 *
	 * @return bool
	 */
	protected function checkRoles(Utils\ArrayHash $element) : bool
	{
		// Check if element has role parameter
		if ($element->offsetExists('role')) {
			$roles = (array) $element->offsetGet('role');

			foreach ($roles as $role) {
				if ($this->user->isInRole($role)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		return TRUE;
	}
}
