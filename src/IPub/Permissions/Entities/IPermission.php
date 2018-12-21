<?php
/**
 * IPermission.php
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

interface IPermission
{
	/**
	 * Permission string delimiter
	 *
	 * @var string
	 */
	public const DELIMITER = ':';

	/**
	 * Get permission resource
	 *
	 * @return IResource|NULL
	 */
	function getResource() : ?IResource;

	/**
	 * Get permission privilege
	 *
	 * @return string
	 */
	function getPrivilege() : ?string;

	/**
	 * Get permission assertion callback
	 * 
	 * @return callable|NULL
	 */
	public function getAssertion() : ?callable;

	/**
	 * Set permission details like title, description, etc.
	 *
	 * @param array $details
	 * 
	 * @return void
	 */
	function setDetails(array $details) : void;

	/**
	 * Get permission title
	 *
	 * @return string|NULL
	 */
	function getTitle() : ?string;

	/**
	 * Get permission title
	 *
	 * @return string|NULL
	 */
	function getDescription() : ?string;
}
