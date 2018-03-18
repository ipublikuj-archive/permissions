<?php
/**
 * RolesProvider.php
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

use Nette;
use Nette\Utils;

use IPub\Permissions\Entities;
use IPub\Permissions\Exceptions;

/**
 * Basic resources provider
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Providers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class ResourcesProvider implements IResourcesProvider
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var Entities\IResource[]
	 */
	private $resources = [];

	/**
	 * @param string $id
	 * @param Entities\IResource|NULL $parent
	 *
	 * @return Entities\IResource
	 */
	public function addResource(string $id, ?Entities\IResource $parent = NULL) : Entities\IResource
	{
		if (array_key_exists($id, $this->resources)) {
			return $this->resources[$id];
		}

		$resource = new Entities\Resource($id);

		if ($parent) {
			$resource->setParent($parent);
		}

		$this->resources[$id] = $resource;

		return $resource;
	}

	/**
	 * @param string $id
	 *
	 * @return Entities\IResource
	 */
	public function getResource(string $id) : Entities\IResource
	{
		if (!array_key_exists($id, $this->resources)) {
			throw new Exceptions\InvalidStateException(sprintf('Resource "%s" is not registered.', $id));
		}

		return $this->resources[$id];
	}

	/**
	 * @return Entities\IResource[]
	 */
	public function findAll() : array
	{
		return $this->resources;
	}
}
