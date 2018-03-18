<?php
/**
 * IResource.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
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
	 * @param IResource|NULL $parent
	 *
	 * @return void
	 */
	function setParent(?IResource $parent = NULL) : void;

	/**
	 * @return IResource|NULL
	 */
	function getParent() : ?IResource;

	/**
	 * @param IResource[] $resources
	 *
	 * @return void
	 */
	function setChildren(array $resources) : void;

	/**
	 * @param IResource $resource
	 *
	 * @return void
	 */
	function addChild(IResource $resource) : void;

	/**
	 * @return IResource[]
	 */
	function getChildren() : array;

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	function setName(string $name) : void;

	/**
	 * @return string|NULL
	 */
	function getName() : ?string;

	/**
	 * @param string $comment
	 *
	 * @return void
	 */
	function setComment(string $comment) : void;

	/**
	 * @return string|NULL
	 */
	function getComment() : ?string;
}
