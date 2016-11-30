<?php
/**
 * IResource.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Entities
 * @since          2.0.0
 *
 * @date           30.11.16
 */

declare(strict_types = 1);

namespace IPub\Permissions\Entities;

use Nette;
use Nette\Security as NS;

interface IResource extends NS\IResource
{
	/**
	 * @param IResource $parent
	 *
	 * @return void
	 */
	function setParent(IResource $parent = NULL);

	/**
	 * @return IResource
	 */
	function getParent();

	/**
	 * @param IResource[] $resources
	 *
	 * @return void
	 */
	function setChildren(array $resources);

	/**
	 * @param IResource $resource
	 *
	 * @return void
	 */
	function addChild(IResource $resource);

	/**
	 * @return IResource[]
	 */
	function getChildren();

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	function setName(string $name);

	/**
	 * @return string|NULL
	 */
	function getName();

	/**
	 * @param string $comment
	 *
	 * @return void
	 */
	function setComment(string $comment);

	/**
	 * @return string|NULL
	 */
	function getComment();
}
