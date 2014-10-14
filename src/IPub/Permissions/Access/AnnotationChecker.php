<?php
/**
 * AnnotationChecker.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Access
 * @since		5.0
 *
 * @date		13.10.14
 */

namespace IPub\Permissions\Access;

use Nette;
use Nette\Utils;
use Nette\Security as NS;

use IPub;
use IPub\Permissions\Exceptions;
use IPub\Permissions\Security;

class AnnotationChecker extends Nette\Object implements IChecker, ICheckRequirements
{
	/**
	 * @var NS\User
	 */
	protected $user;

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
	public function isAllowed($element)
	{
		// Check annotations only if element have to be secured
		if ($element instanceof \Reflector && $element->hasAnnotation('Secured')) {
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
	 * @param \Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	protected function checkUser(\Reflector $element)
	{
		// Check if element has @Secured\User annotation
		if ($element->hasAnnotation('Secured\User')) {
			// Get user annotation
			$user = $element->getAnnotation('Secured\User');

			// Annotation is single string
			if (is_string($user)) {
				// User have to be logged in and is not
				if ($user == 'loggedIn' && !$this->user->isLoggedIn()) {
					return FALSE;

				// User have to be logged out and is logged in
				} else if ($user == 'guest' && $this->user->isLoggedIn()) {
					return FALSE;
				}

			// Annotation have multiple definitions
			} else {
				throw new Exceptions\InvalidArgumentException('In @Security\User annotation are allowed only two strings: \'loggedIn\' & \'guest\'');
			}

			return TRUE;
		}

		return TRUE;
	}

	/**
	 * @param \Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	protected function checkResources(\Reflector $element)
	{
		// Check if element has @Security\Resource annotation & @Secured\Privilege annotation
		if ($element->hasAnnotation('Secured\Resource')) {
			$resources	= (array) $element->getAnnotation('Secured\Resource');
			$privileges	= $element->hasAnnotation('Secured\Privilege') ? (array) $element->getAnnotation('Secured\Privilege') : [];

			if (count($resources) != 1) {
				throw new Exceptions\InvalidStateException('Invalid resources count in @Security\Resource annotation!');
			}

			foreach ($resources as $resource) {
				if (count($privileges)) {
					foreach($privileges as $privilege) {
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
	 * @param \Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	protected function checkPrivileges(\Reflector $element)
	{
		// Check if element has @Secured\Privilege annotation & hasn't @Secured\Resource annotation
		if (!$element->hasAnnotation('Secured\Resource') && $element->hasAnnotation('Secured\Privilege')) {
			$privileges = (array) $element->getAnnotation('Secured\Privilege');

			if (count($privileges) != 1) {
				throw new Exceptions\InvalidStateException('Invalid privileges count in @Security\Privilege annotation!');
			}

			foreach($privileges as $privilege) {
				if ($this->user->isAllowed(NS\IAuthorizator::ALL, $privilege)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param \Reflector $element
	 *
	 * @return bool
	 */
	protected function checkPermission(\Reflector $element)
	{
		// Check if element has @Secured\Permission annotation
		if ($element->hasAnnotation('Secured\Permission')) {
			$permissions = (array) $element->getAnnotation('Secured\Permission');

			foreach($permissions as $permission) {
				// Parse resource & privilege from permission
				list($resource, $privilege) = explode(Security\Permission::DELIMITER, $permission);

				// Remove white spaces
				$resource	= Utils\Strings::trim($resource);
				$privilege	= Utils\Strings::trim($privilege);

				if ($this->user->isAllowed($resource, $privilege)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param \Reflector $element
	 *
	 * @return bool
	 */
	protected function checkRoles(\Reflector $element)
	{
		// Check if element has @Secured\Role annotation
		if ($element->hasAnnotation('Secured\Role')) {
			$roles = (array) $element->getAnnotation('Secured\Role');

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