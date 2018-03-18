<?php
/**
 * IResourcesProvider.php
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
 * Permission resources provider interface
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IResourcesProvider
{
	/**
	 * @return Entities\IResource[]
	 */
	public function findAll() : array;
}
