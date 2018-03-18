<?php
/**
 * Test: IPub\Permissions\Libraries
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Tests
 * @since          2.0.0
 *
 * @date           01.12.16
 */

declare(strict_types = 1);

namespace IPubTests\Permissions\Libs;

use IPub\Permissions\Providers;

class ResourcesProvider extends Providers\ResourcesProvider
{
	public function __construct()
	{
		$this->addResource('firstResource');
		$this->addResource('secondResource');
		$this->addResource('thirdResource');
	}
}
