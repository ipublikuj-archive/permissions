<?php
/**
 * IRolesProvider.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 * @since          1.0.0
 *
 * @date           10.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Providers;

use IPub;
use IPub\Permissions;
use IPub\Permissions\Entities;

/**
 * Roles provider interface
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IRolesProvider
{
	/**
	 * @return Entities\IRole[]
	 */
	public function findAll() : array;
}
