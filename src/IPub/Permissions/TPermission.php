<?php
/**
 * TPermission.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	common
 * @since		5.0
 *
 * @date		13.10.14
 */

namespace IPub\Permissions;

use Nette;
use Nette\Application;

use IPub;
use IPub\Permissions\Security;

trait TPermission
{
	/**
	 * @var Security\Permission
	 */
	protected $permission;

	/**
	 * @var Access\ICheckRequirements
	 */
	protected $requirementsChecker;

	/**
	 * @param Security\Permission $permission
	 * @param Access\ICheckRequirements $requirementsChecker
	 */
	public function injectPermission(
		Security\Permission $permission,
		Access\ICheckRequirements $requirementsChecker
	) {
		$this->permission			= $permission;
		$this->requirementsChecker	= $requirementsChecker;
	}

	/**
	 * @param $element
	 *
	 * @throws Application\ForbiddenRequestException
	 */
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);

		if (!$this->requirementsChecker->isAllowed($element)) {
			throw new Application\ForbiddenRequestException;
		}
	}
}