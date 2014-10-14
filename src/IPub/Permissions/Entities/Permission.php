<?php
/**
 * Permission.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Entities
 * @since		5.0
 *
 * @date		13.03.14
 */

namespace IPub\Permissions\Entities;

use Nette;
use Nette\Utils;

use IPub;
use IPub\Permissions\Security;

class Permission extends Nette\Object implements IPermission
{
	/**
	 * Permission resource
	 *
	 * @var string
	 */
	protected $resource;

	/**
	 * Permission privilege
	 *
	 * @var string
	 */
	protected $privilege;

	/**
	 * Permission details
	 *
	 * @var array
	 */
	protected $details = [];

	/**
	 * @param string $resource
	 * @param string $privilege
	 * @param array $details
	 */
	public function __construct($resource, $privilege, $details = [])
	{
		$this->resource		= $resource;
		$this->privilege	= $privilege;

		// Check if permission details are provided too
		if (!empty($details)) {
			$this->details = Utils\ArrayHash::from($details);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPrivilege()
	{
		return $this->privilege;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDetails(array $details)
	{
		$this->details = Utils\ArrayHash::from($details);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return isset($this->details->title) ? $this->details->title : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription()
	{
		return isset($this->details['description']) ? $this->details['description'] : NULL;
	}

	/**
	 * Convert permission object to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->resource . Security\Permission::DELIMITER . $this->privilege;
	}
}