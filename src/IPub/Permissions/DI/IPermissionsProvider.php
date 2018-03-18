<?php
/**
 * IPermissionsProvider.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           12.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\DI;

/**
 * Extension providers interface for permissions
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IPermissionsProvider
{
	/**
	 * Return array of permissions
	 *
	 * @return array
	 */
	function getPermissions() : array;
}
