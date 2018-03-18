<?php
/**
 * Permission.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           13.03.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Entities;

use Nette;
use Nette\Utils;

class Permission implements IPermission
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * Permission resource
	 *
	 * @var string
	 */
	private $resource;

	/**
	 * Permission privilege
	 *
	 * @var string
	 */
	private $privilege;

	/**
	 * @var callable|NULL
	 */
	private $assertion;

	/**
	 * Permission details
	 *
	 * @var Utils\ArrayHash
	 */
	private $details;

	/**
	 * @param IResource|NULL $resource
	 * @param string|NULL $privilege
	 * @param array $details
	 * @param callable|NULL $assertion
	 */
	public function __construct(?IResource $resource = NULL, ?string $privilege = NULL, $details = [], ?callable $assertion = NULL)
	{
		$this->resource = $resource;
		$this->privilege = $privilege;
		$this->assertion = $assertion;

		// Check if permission details are provided too
		if (!empty($details)) {
			$this->details = Utils\ArrayHash::from($details);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResource() : ?IResource
	{
		return $this->resource;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPrivilege() : string
	{
		return $this->privilege;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAssertion() : ?callable
	{
		return $this->assertion;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDetails(array $details) : void
	{
		$this->details = Utils\ArrayHash::from($details);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle() : ?string
	{
		return $this->details->offsetExists('title') ? $this->details->offsetGet('title') : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription() : ?string
	{
		return $this->details->offsetExists('description') ? $this->details->offsetGet('description') : NULL;
	}

	/**
	 * Convert permission object to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return ((string) $this->resource) . self::DELIMITER . $this->privilege;
	}
}
