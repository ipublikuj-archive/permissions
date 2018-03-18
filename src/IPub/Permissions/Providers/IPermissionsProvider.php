<?php
/**
 * IPermissionsProvider.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 * @since          2.0.0
 *
 * @date           30.11.16
 */

declare(strict_types = 1);

namespace IPub\Permissions\Providers;

use IPub\Permissions\Entities;

/**
 * Permissions provider interface
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IPermissionsProvider
{
	/**
	 * @return Entities\IPermission[]
	 */
	public function findAll() : array;
}
