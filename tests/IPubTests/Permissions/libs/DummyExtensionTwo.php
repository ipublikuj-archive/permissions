<?php
/**
 * Test: IPub\Permissions\Libraries
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
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

use IPub;
use IPub\Permissions;

class DummyExtensionTwo extends DI\CompilerExtension implements Permissions\DI\IPermissionsProvider
{
	public function getPermissions() : array
	{
		return [
			'wrongStringVersion' => [
				'title'       => 'This is example title',
				'description' => 'This is example description'
			],
		];
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'dummyTwo')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new DummyExtensionTwo());
		};
	}
}
