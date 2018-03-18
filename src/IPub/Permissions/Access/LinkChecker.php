<?php
/**
 * LinkChecker.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 * @since          1.0.0
 *
 * @date           13.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Access;

use Nette;
use Nette\Application;
use Nette\Application\UI;

/**
 * Create link access checker
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class LinkChecker implements IChecker
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var Application\IPresenterFactory
	 */
	private $presenterFactory;

	/**
	 * @var Application\Application
	 */
	private $application;

	/**
	 * @var ICheckRequirements
	 */
	private $requirementsChecker;

	/**
	 * @param Application\IPresenterFactory $presenterFactory
	 * @param Application\Application $application
	 * @param ICheckRequirements $requirementsChecker
	 */
	function __construct(
		Application\IPresenterFactory $presenterFactory,
		Application\Application $application,
		ICheckRequirements $requirementsChecker
	) {
		$this->presenterFactory = $presenterFactory;
		$this->application = $application;

		// Permission annotation access checker
		$this->requirementsChecker = $requirementsChecker;
	}

	/**
	 * Check whenever current user is allowed to use given link
	 *
	 * @param string $element etc "this", ":Admin:Show:default"
	 *
	 * @return bool
	 *
	 * @throws Application\InvalidPresenterException
	 * @throws \ReflectionException
	 */
	public function isAllowed($element) : bool
	{
		list($presenter, $action) = $this->formatLink($element);

		$presenterReflection = new UI\ComponentReflection($this->presenterFactory->getPresenterClass($presenter));

		if (!$this->requirementsChecker->isAllowed($presenterReflection)) {
			return FALSE;
		}

		$actionKey = UI\Presenter::ACTION_KEY . ucfirst($action);

		if ($presenterReflection->hasMethod($actionKey) && !$this->requirementsChecker->isAllowed($presenterReflection->getMethod($actionKey))) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Format link to format array('module:submodule:presenter', 'action')
	 *
	 * @param string $destination
	 *
	 * @return array(presenter, action)
	 */
	public function formatLink(string $destination) : array
	{
		if ($destination === 'this') {
			return [$this->application->getPresenter()->getName(), $this->application->getPresenter()->getAction()];
		}

		$parts = explode(':', $destination);

		if ($destination[0] != ':') {
			$current = explode(':', $this->application->getPresenter()->getName());

			if (strpos($destination, ':') !== FALSE) {
				// Remove presenter
				array_pop($current);
			}

			$parts = array_merge($current, $parts);

		} else {
			// Remove empty
			array_shift($parts);
		}

		if ($destination[strlen($destination) - 1] == ':') {
			// Remove empty
			array_pop($parts);

			$action = UI\Presenter::DEFAULT_ACTION;

		} else {
			$action = array_pop($parts);
		}

		return [implode(':', $parts), $action];
	}
}
