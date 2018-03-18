<?php
/**
 * IPermissionsProvider.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 * @since          2.0.0
 *
 * @date           30.11.16
 */

declare(strict_types = 1);

namespace IPub\Permissions\DI;

/**
 * Extension providers interface for resources
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IResourcesProvider
{
	/**
	 * Return array of access resources
	 *
	 * @return array
	 */
	function getResources() : array;
}
