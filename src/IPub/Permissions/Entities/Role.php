<?php
/**
 * Role.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Entities
 * @since		5.0
 *
 * @date		12.03.14
 */

namespace IPub\Permissions\Entities;

use Nette;

class Role extends Nette\Object implements IRole
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var IRole
	 */
	protected $parent;

	/**
	 * @var array
	 */
	protected $children;

	/**
	 * @var string
	 */
	protected $keyName;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $comment;

	/**
	 * @var int
	 */
	protected $priority = 0;

	/**
	 * @var array
	 */
	protected $permissions = [];

	/**
	 * {@inheritdoc}
	 */
	public function setParent(IRole $parent = NULL)
	{
		$this->parent = $parent;

		return $this;
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
	public function setChildren($roles)
	{
		$this->children[] = $roles;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setKeyName($keyName)
	{
		$this->keyName = (string) $keyName;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKeyName()
	{
		return $this->keyName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName($name)
	{
		$this->name = (string) $name;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setComment($comment)
	{
		$this->comment = (string) $comment;

		return $this;
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
	public function setPriority($priority)
	{
		$this->priority = (int) $priority;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPermissions($permissions)
	{
		$this->permissions = $permissions;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasPermission($permission)
	{
		return in_array((string) $permission, $this->permissions);
	}

	/**
	 * {@inheritdoc}
	 */
	public function addPermission($permission)
	{
		$this->permissions[] = (string) $permission;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function clearPermissions()
	{
		$this->permissions = [];

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isLocked()
	{
		return in_array($this->keyName, [self::ROLE_ANONYMOUS, self::ROLE_AUTHENTICATED, self::ROLE_ADMINISTRATOR]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAnonymous()
	{
		return $this->keyName == self::ROLE_ANONYMOUS;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAuthenticated()
	{
		return $this->keyName == self::ROLE_AUTHENTICATED;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAdministrator()
	{
		return $this->keyName == self::ROLE_ADMINISTRATOR;
	}

	/**
	 * Convert role object to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}