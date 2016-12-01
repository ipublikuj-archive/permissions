<?php
/**
 * Test: IPub\Permissions\Libraries
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Tests
 * @since          2.0.0
 *
 * @date           01.12.16
 */

declare(strict_types = 1);

namespace IPubTests\Permissions\Libs;

use Nette;

use IPub;
use IPub\Permissions;
use IPub\Permissions\Providers;

class PermissionsProvider extends Providers\PermissionsProvider
{
	public function __construct(ResourcesProvider $resourcesProvider)
	{
		$this->addPermission($resourcesProvider->getResource('firstResource'), 'firstPrivilege', [
			'title'       => 'This is first example title',
			'description' => 'This is first example description',
		]);

		$this->addPermission($resourcesProvider->getResource('secondResource'), 'secondPrivilege', [
			'title'       => 'This is second example title',
			'description' => 'This is second example description',
		]);

		$this->addPermission($resourcesProvider->getResource('thirdResource'), 'thirdPrivilege', [
			'title'       => 'This is third example title',
			'description' => 'This is third example description',
		]);
	}
}
