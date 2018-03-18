<?php
/**
 * IPermissionsProvider.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 * @since          2.0.0
 *
 * @date           30.11.16
 */

declare(strict_types = 1);

namespace IPub\Permissions\DI;

/**
 * Extension providers interface for roles
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IRolesProvider
{
	/**
	 * Return array of users roles
	 *
	 * @return array
	 */
	function getRoles() : array;
}
