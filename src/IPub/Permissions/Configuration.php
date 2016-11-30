<?php
/**
 * Configuration.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     common
 * @since          2.0.0
 *
 * @date           17.02.15
 */

namespace IPub\Permissions;

use Nette;
use Nette\Application;

/**
 * Permissions's extension configuration storage. Store basic extension settings
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class Configuration
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var string|NULL
	 */
	private $redirectUrl;

	/**
	 * @var Application\LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @param string|NULL $redirectUrl
	 * @param Application\LinkGenerator $linkGenerator
	 */
	public function __construct(string $redirectUrl = NULL, Application\LinkGenerator $linkGenerator)
	{
		$this->redirectUrl = $redirectUrl;
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * Build the URL for redirection if is set
	 *
	 * @param array $params
	 *
	 * @return string|NULL
	 *
	 * @throws Application\UI\InvalidLinkException
	 */
	public function getRedirectUrl(array $params = [])
	{
		if ($this->redirectUrl) {
			return $this->linkGenerator->link($this->redirectUrl, $params);
		}

		return NULL;
	}
}
