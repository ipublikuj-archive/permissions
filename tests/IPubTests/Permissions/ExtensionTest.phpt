<?php
/**
 * Test: IPub\Permissions\Extension
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		13.01.15
 */

namespace IPubTests\Permissions;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\Permissions;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/RolesModel.php';

class ExtensionTest extends Tester\TestCase
{
	/**
	 * @return \SystemContainer|\Nette\DI\Container
	 */
	protected function createContainer()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		Permissions\DI\PermissionsExtension::register($config);

		$config->addConfig(__DIR__ . '/files/config.neon', $config::NONE);

		return $config->createContainer();
	}

	public function testFunctional()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('permissions.permissions') instanceof IPub\Permissions\Security\Permission);
		Assert::true($dic->getService('permissions.checkers.annotation') instanceof IPub\Permissions\Access\AnnotationChecker);
		Assert::true($dic->getService('permissions.checkers.latte') instanceof IPub\Permissions\Access\LatteChecker);
		Assert::true($dic->getService('permissions.checkers.link') instanceof IPub\Permissions\Access\LinkChecker);
	}
}

\run(new ExtensionTest());