<?php
/**
 * IPermission.php
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

interface IPermission
{
	/**
	 * Get permission resource
	 *
	 * @return string
	 */
	public function getResource();

	/**
	 * Get permission privilege
	 *
	 * @return string
	 */
	public function getPrivilege();

	/**
	 * Set permission details like title, description, etc.
	 *
	 * @param array $details
	 *
	 * @return $this
	 */
	public function setDetails(array $details);

	/**
	 * Get permission title
	 *
	 * @return string|null
	 */
	public function getTitle();

	/**
	 * Get permission title
	 *
	 * @return string|null
	 */
	public function getDescription();
}