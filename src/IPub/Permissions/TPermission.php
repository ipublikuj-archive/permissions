<?php
/**
 * TPermission.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           13.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions;

use Nette\Application;

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
	 * @param mixed $element
	 *
	 * @return void
	 *
	 * @throws Application\ForbiddenRequestException
	 * @throws Application\UI\InvalidLinkException
	 */
	public function checkRequirements($element) : void
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
