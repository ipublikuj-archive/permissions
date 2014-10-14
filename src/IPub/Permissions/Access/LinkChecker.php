<?php
/**
 * LinkChecker.php
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
use Nette\Application;
use Nette\Application\UI;
use Nette\Security as NS;

class LinkChecker extends Nette\Object implements IChecker
{
	/**
	 * @var Application\IPresenterFactory
	 */
	protected $presenterFactory;

	/**
	 * @var Application\Application
	 */
	protected $application;

	/**
	 * @var ICheckRequirements
	 */
	protected $requirementsChecker;

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
		$this->presenterFactory	= $presenterFactory;
		$this->application		= $application;

		// Permission annotation access checker
		$this->requirementsChecker = $requirementsChecker;
	}

	/**
	 * Check whenever current user is allowed to use given link
	 *
	 * @param string $element etc "this", ":Admin:Show:default"
	 *
	 * @return bool
	 */
	public function isAllowed($element)
	{
		list($presenter, $action) = $this->formatLink($element);

		$presenterReflection = UI\PresenterComponentReflection::from($this->presenterFactory->getPresenterClass($presenter));

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
	public function formatLink($destination)
	{
		if ($destination == 'this') {
			return array($this->application->getPresenter()->getName(), $this->application->getPresenter()->getAction());
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

		return array(implode(':', $parts), $action);
	}
}