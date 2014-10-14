<?php
/**
 * IPermissionsProvider.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		12.10.14
 */

namespace IPub\Permissions\DI;

use Nette;

use IPub;

interface IPermissionsProvider
{
	/**
	 * Return array of permissions
	 *
	 * @return array
	 */
	function getPermissions();
}
