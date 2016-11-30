<?php
/**
 * IPermission.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           13.03.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Entities;

interface IPermission
{
	/**
	 * Permission string delimiter
	 *
	 * @var string
	 */
	const DELIMITER = ':';

	/**
	 * Get permission resource
	 *
	 * @return IResource|NULL
	 */
	function getResource();

	/**
	 * Get permission privilege
	 *
	 * @return string
	 */
	function getPrivilege();

	/**
	 * Get permission assertion callback
	 * 
	 * @return callable|NULL
	 */
	public function getAssertion();

	/**
	 * Set permission details like title, description, etc.
	 *
	 * @param array $details
	 * 
	 * @return void
	 */
	function setDetails(array $details);

	/**
	 * Get permission title
	 *
	 * @return string|null
	 */
	function getTitle();

	/**
	 * Get permission title
	 *
	 * @return string|null
	 */
	function getDescription();
}
