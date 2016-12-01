<?php
/**
 * AnnotationChecker.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 * @since          1.0.0
 *
 * @date           13.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Access;

use Nette;
use Nette\Application\UI;
use Nette\Utils;
use Nette\Security as NS;

use IPub;
use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;
use IPub\Permissions\Security;

/**
 * Presenter & component annotation access checker
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class AnnotationChecker extends Nette\Object implements IChecker, ICheckRequirements
{
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
		if (
			$element instanceof \Reflector
			&& $element->hasAnnotation('Secured')
		) {
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
	 * @param UI\ComponentReflection|UI\MethodReflection|Nette\Reflection\ClassType|Nette\Reflection\Method|\Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	private function checkUser($element) : bool
	{
		// Check if element has @Secured\User annotation
		if ($element->hasAnnotation('Secured\User')) {
			// Get user annotation
			$user = $element->getAnnotation('Secured\User');

			// Annotation is single string
			if (is_string($user) && in_array($user, ['loggedIn', 'guest'], TRUE)) {
				// User have to be logged in and is not
				if ($user === 'loggedIn' && $this->user->isLoggedIn() === FALSE) {
					return FALSE;

				// User have to be logged out and is logged in
				} elseif ($user === 'guest' && $this->user->isLoggedIn() === TRUE) {
					return FALSE;
				}

			// Annotation have wrong definition
			} else {
				throw new Exceptions\InvalidArgumentException('In @Security\User annotation is allowed only one from two strings: \'loggedIn\' & \'guest\'');
			}

			return TRUE;
		}

		return TRUE;
	}

	/**
	 * @param UI\ComponentReflection|UI\MethodReflection|Nette\Reflection\ClassType|Nette\Reflection\Method|\Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	private function checkResources($element) : bool
	{
		// Check if element has @Security\Resource annotation & @Secured\Privilege annotation
		if ($element->hasAnnotation('Secured\Resource')) {
			$resources = $this->getElementAttribute($element, 'Secured\Resource');

			if (count($resources) != 1) {
				throw new Exceptions\InvalidStateException('Invalid resources count in @Security\Resource annotation!');
			}

			$privileges = $this->getElementAttribute($element, 'Secured\Privilege');

			foreach ($resources as $resource) {
				if ($privileges !== FALSE) {
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
	 * @param UI\ComponentReflection|UI\MethodReflection|Nette\Reflection\ClassType|Nette\Reflection\Method|\Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	private function checkPrivileges($element) : bool
	{
		// Check if element has @Secured\Privilege annotation & hasn't @Secured\Resource annotation
		if (!$element->hasAnnotation('Secured\Resource') && $element->hasAnnotation('Secured\Privilege')) {
			$privileges = $this->getElementAttribute($element, 'Secured\Privilege');

			if (count($privileges) != 1) {
				throw new Exceptions\InvalidStateException('Invalid privileges count in @Security\Privilege annotation!');
			}

			foreach ($privileges as $privilege) {
				// Check if privilege name is defined
				if ($privilege === TRUE) {
					continue;
				}

				if ($this->user->isAllowed(NS\IAuthorizator::ALL, $privilege)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param UI\ComponentReflection|UI\MethodReflection|Nette\Reflection\ClassType|Nette\Reflection\Method|\Reflector $element
	 *
	 * @return bool
	 */
	private function checkPermission($element) : bool
	{
		// Check if element has @Secured\Permission annotation
		if ($element->hasAnnotation('Secured\Permission')) {
			$permissions = $this->getElementAttribute($element, 'Secured\Permission');

			foreach ($permissions as $permission) {
				// Check if parameters are defined
				if ($permission === TRUE) {
					continue;
				}

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
	 * @param UI\ComponentReflection|UI\MethodReflection|Nette\Reflection\ClassType|Nette\Reflection\Method|\Reflector $element
	 *
	 * @return bool
	 */
	private function checkRoles($element) : bool
	{
		// Check if element has @Secured\Role annotation
		if ($element->hasAnnotation('Secured\Role')) {
			$roles = $this->getElementAttribute($element, 'Secured\Role');

			foreach ($roles as $role) {
				// Check if role name is defined
				if ($role === TRUE) {
					continue;
				}

				if ($this->user->isInRole($role)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param UI\ComponentReflection|UI\MethodReflection|Nette\Reflection\ClassType|Nette\Reflection\Method|\Reflector $element
	 * @param string $attribute
	 *
	 * @return array|FALSE
	 */
	private function getElementAttribute($element, string $attribute)
	{
		if (class_exists(UI\ComponentReflection::class)) {
			return UI\ComponentReflection::parseAnnotation($element, $attribute);
		}

		$values = (array) $element->getAnnotation($attribute);

		return is_array($values) ? $values : ($values ? [$values] : FALSE);
	}
}
