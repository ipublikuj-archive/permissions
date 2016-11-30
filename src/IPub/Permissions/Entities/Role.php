<?php
/**
 * Role.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           12.03.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Entities;

use Nette;

use IPub;
use IPub\Permissions\Exceptions;

class Role implements IRole
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
	 * @var IRole|NULL
	 */
	protected $parent;

	/**
	 * @var \SplObjectStorage
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

	/**
	 * @var \SplObjectStorage
	 */
	protected $permissions;

	/**
	 * @param string $id
	 * @param string|NULL $name
	 * @param string|NULL $comment
	 */
	public function __construct(string $id, string $name = NULL, string $comment = NULL)
	{
		// Role identifier
		$this->id = $id;

		$this->name = $name;
		$this->comment = $comment;

		// Storage initialization
		$this->permissions = new \SplObjectStorage();
		$this->children = new \SplObjectStorage();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setParent(IRole $parent = NULL)
	{
		$this->parent = $parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setChildren(array $roles)
	{
		$this->children = new \SplObjectStorage();

		foreach ($roles as $child) {
			if ($child instanceof IRole) {
				$this->children->attach($child);
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function addChild(IRole $role)
	{
		if (!$this->children->contains($role)) {
			$this->children->attach($role);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getChildren() : array
	{
		return $this->children;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRoleId() : string
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name ?: $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setComment(string $comment)
	{
		$this->comment = $comment;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPermissions(array $permissions)
	{
		$this->permissions = new \SplObjectStorage();

		foreach ($permissions as $permission) {
			if ($permission instanceof IPermission) {
				$this->permissions->attach($permission);
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function addPermission(IPermission $permission)
	{
		if (!$this->permissions->contains($permission)) {
			$this->permissions->attach($permission);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPermissions() : array
	{
		return $this->permissions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasPermission(IPermission $permission) : bool
	{
		return $this->permissions->contains($permission);
	}

	/**
	 * {@inheritdoc}
	 */
	public function removePermission(IPermission $permission)
	{
		if (!$this->permissions->contains($permission)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Permission "%s" cannot be removed since it is not associated with the role %s', $permission, $this->getName()));
		}

		$this->permissions->detach($permission);
	}

	/**
	 * {@inheritdoc}
	 */
	public function clearPermissions()
	{
		$this->permissions = new \SplObjectStorage();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isLocked() : bool
	{
		return in_array($this->id, [IRole::ROLE_ANONYMOUS, IRole::ROLE_AUTHENTICATED, IRole::ROLE_ADMINISTRATOR], TRUE);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAnonymous() : bool
	{
		return $this->id === IRole::ROLE_ANONYMOUS;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAuthenticated() : bool
	{
		return $this->id === IRole::ROLE_AUTHENTICATED;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAdministrator() : bool
	{
		return $this->id === IRole::ROLE_ADMINISTRATOR;
	}

	/**
	 * Convert role object to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->id;
	}
}
