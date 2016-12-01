<?php
/**
 * TPermission.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           13.10.14
 */

namespace IPub\Permissions;

use Nette;
use Nette\Application;

use IPub;
use IPub\Permissions\Security;

/**
 * Helper trait
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
trait TPermission
{
	/**
	 * @var Configuration
	 */
	protected $permissionConfiguration;

	/**
	 * @var Access\ICheckRequirements
	 */
	protected $requirementsChecker;

	/**
	 * @param Access\ICheckRequirements $requirementsChecker
	 * @param Configuration $configuration
	 */
	public function injectPermission(
		Access\ICheckRequirements $requirementsChecker,
		Configuration $configuration
	) {
		$this->requirementsChecker = $requirementsChecker;
		$this->permissionConfiguration = $configuration;
	}

	/**
	 * @param $element
	 *
	 * @throws Application\ForbiddenRequestException
	 */
	public function checkRequirements($element)
	{
		$redirectUrl = $this->permissionConfiguration->getRedirectUrl([
			'backlink' => $this->storeRequest(),
		]);

		try {
			parent::checkRequirements($element);

			if (!$this->requirementsChecker->isAllowed($element)) {
				throw new Application\ForbiddenRequestException;
			}

		} catch (Application\ForbiddenRequestException $ex) {
			if ($redirectUrl) {
				$this->getPresenter()->redirectUrl($redirectUrl);

			} else {
				throw $ex;
			}
		}
	}
}
