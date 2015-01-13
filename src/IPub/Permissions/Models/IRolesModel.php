<?php
/**
 * IRolesModel.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Models
 * @since		5.0
 *
 * @date		10.10.14
 */

namespace IPub\Permissions\Models;

use IPub;
use IPub\Permissions;

interface IRolesModel
{
	/**
	 * @return Permissions\Entities\IRole[]
	 */
	public function findAll();
}