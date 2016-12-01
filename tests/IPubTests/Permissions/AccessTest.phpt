<?php
/**
 * Test: IPub\Permissions\Access
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Permissions!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           14.01.15
 */

declare(strict_types = 1);

namespace IPubTests\Permissions;

use Nette;
use Nette\Application;
use Nette\Application\UI;
use Nette\Security as NS;

use Tester;
use Tester\Assert;

use IPub;
use IPub\Permissions;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require __DIR__ . DS . 'libs' . DS . 'ResourcesProvider.php';
require __DIR__ . DS . 'libs' . DS . 'PermissionsProvider.php';
require __DIR__ . DS . 'libs' . DS . 'RolesProvider.php';

class AccessTest extends Tester\TestCase
{
	/**
	 * @var Application\IPresenterFactory
	 */
	private $presenterFactory;

	/**
	 * @var Nette\DI\Container
	 */
	private $container;

	/**
	 * @var NS\User
	 */
	private $user;

	/**
	 * @return array[]|array
	 */
	public function dataRegisteredUsers() : array
	{
		return [
			['john', '123456'],
			['jane', '123456'],
		];
	}

	public function dataGuestUsers() : array
	{
		return [
			['guest']
		];
	}

	/**
	 * Set up
	 */
	public function setUp()
	{
		parent::setUp();

		$this->container = $this->createContainer();

		// Get presenter factory from container
		$this->presenterFactory = $this->container->getByType(Nette\Application\IPresenterFactory::class);

		// Get application user
		$this->user = $this->container->getService('user');

		// Create user authenticator
		$authenticator = new Nette\Security\SimpleAuthenticator([
			'john' => '123456',
			'jane' => '123456',
		], [
			'john' => [
				Permissions\Entities\IRole::ROLE_AUTHENTICATED
			],
			'jane' => [
				Permissions\Entities\IRole::ROLE_AUTHENTICATED,
				Permissions\Entities\IRole::ROLE_ADMINISTRATOR
			]
		]);
		$this->user->setAuthenticator($authenticator);
	}

	/**
	 * @dataProvider dataRegisteredUsers
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function testPresenterActionAllowed(string $username, string $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'allowed']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		// Logout user
		$this->user->logout(TRUE);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::equal('Passed', $response->getSource());
	}

	/**
	 * @dataProvider dataRegisteredUsers
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function testPresenterActionAllowedRole(string $username, string $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'allowedRole']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		// Logout user
		$this->user->logout(TRUE);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::equal('Passed', $response->getSource());
	}

	/**
	 * @dataProvider dataGuestUsers
	 *
	 * @param string $username
	 *
	 * @throws Nette\Application\ForbiddenRequestException
	 */
	public function testPresenterActionNotAllowed(string $username)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'allowedRole']);
		// & fire presenter
		$presenter->run($request);
	}

	/**
	 * @dataProvider dataRegisteredUsers
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @throws Nette\Application\ForbiddenRequestException
	 */
	public function testNotAllowedLoggedIn(string $username, string $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'onlyGuest']);
		// & fire presenter & catch
		$presenter->run($request);
	}

	/**
	 * @dataProvider dataGuestUsers
	 *
	 * @param string $username
	 */
	public function testAllowedGuest(string $username)
	{
		$this->user->logout(TRUE);

		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'onlyGuest']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::equal('Passed', $response->getSource());
	}

	/**
	 * @return Application\IPresenter
	 */
	protected function createPresenter() : Application\IPresenter
	{
		// Create test presenter
		$presenter = $this->presenterFactory->createPresenter('Test');
		// Disable auto canonicalize to prevent redirection
		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

	/**
	 * @return Nette\DI\Container
	 */
	private function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		Permissions\DI\PermissionsExtension::register($config);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');
		$config->addConfig(__DIR__ . DS . 'files' . DS . 'presenters.neon');

		return $config->createContainer();
	}
}

/**
 * @Secured
 * @Secured\Resource(firstResource)
 * @Secured\Privilege(firstPrivilege)
 */
class TestPresenter extends UI\Presenter
{
	use Permissions\TPermission;

	public function renderAllowed()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Passed'));
	}

	/**
	 * @Secured
	 * @Secured\Role(authenticated, administrator)
	 */
	public function renderAllowedRole()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Passed'));
	}

	/**
	 * @Secured
	 * @Secured\User(guest)
	 */
	public function renderOnlyGuest()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Passed'));
	}
}

\run(new AccessTest());
