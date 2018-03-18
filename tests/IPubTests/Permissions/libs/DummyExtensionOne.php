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

use Nette;
use Nette\DI;

use IPub\Permissions;

class DummyExtensionOne extends DI\CompilerExtension implements Permissions\DI\IPermissionsProvider
{
	public function getPermissions() : array
	{
		return [
			'fromExtensionResourceOne:fromExtensionPrivilegeOne' => [
				'title'       => 'This is example title',
				'description' => 'This is example description',
			],
			new Permissions\Entities\Permission(
				new Permissions\Entities\Resource('fromExtensionResourceTwo'),
				'fromExtensionPrivilegeTwo',
				[
					'title'       => 'This is second example title',
					'description' => 'This is second example description',
				]
			),
			[
				'resource'  => 'fromExtensionResourceThree',
				'privilege' => 'fromExtensionPrivilegeThree',
			],
		];
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'dummyOne')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new DummyExtensionOne());
		};
	}
}
