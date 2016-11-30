<?php
/**
 * ICheckRequirements.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 * @since          1.0.0
 *
 * @date           13.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Access;

/**
 * Requirements checker interface
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Access
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface ICheckRequirements
{
	/**
	 * @param mixed $element
	 *
	 * @return bool
	 */
	function isAllowed($element) : bool;
}
