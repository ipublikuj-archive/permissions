<?php
/**
 * IResource.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Entities
 * @since          2.0.0
 *
 * @date           30.11.16
 */

declare(strict_types = 1);

namespace IPub\Permissions\Entities;

use Nette;

class Resource implements IResource
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var IResource|NULL
	 */
	protected $parent;

	/**
	 * @var IResource[]
	 */
	protected $children;

	/**
	 * @var string|NULL
	 */
	protected $name;

	/**
	 * @var string|NULL
	 */
	protected $comment;

	public function __construct(string $id)
	{
		$this->id = $id;

		$this->children = new \SplObjectStorage();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setParent(IResource $parent = NULL) : void
	{
		$this->parent = $parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent() : ?IResource
	{
		return $this->parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setChildren(array $resources) : void
	{
		$this->children = new \SplObjectStorage();

		foreach ($resources as $child) {
			if ($child instanceof IResource) {
				$this->children->attach($child);
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function addChild(IResource $resource) : void
	{
		if (!$this->children->contains($resource)) {
			$this->children->attach($resource);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getChildren() : array
	{
		$children = [];

		$this->children->rewind();

		while ($this->children->valid())
		{
			$children[] = $this->children->current();
			$this->children->next();
		}

		return $children;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResourceId() : string
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName(string $name) : void
	{
		$this->name = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() : ?string
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setComment(string $comment) : void
	{
		$this->comment = $comment;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getComment() : ?string
	{
		return $this->comment;
	}

	/**
	 * Convert resource object to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->id;
	}
}
