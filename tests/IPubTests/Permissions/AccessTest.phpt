<?php
/**
 * Test: IPub\Permissions\Access
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		14.01.15
 */

namespace IPubTests\Permissions;

use Nette;
use Nette\Application;
use Nette\Application\UI;
use Nette\Security as NS;

use Tester;
use Tester\Assert;

use IPub;
use IPub\Permissions;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/RolesModel.php';

class AccessTest extends Tester\TestCase
{
	/**
	 * @var Permissions\Models\IRolesModel
	 */
	private $rolesModel;

	/**
	 * @var Permissions\Security\Permission
	 */
	private $permission;

	/**
	 * @var Nette\Application\IPresenterFactory
	 */
	private $presenterFactory;

	/**
	 * @var \SystemContainer|\Nette\DI\Container
	 */
	private $container;

	/**
	 * @var NS\User
	 */
	private $user;

	/**
	 * @return array[]|array
	 */
	public function dataPermissions()
	{
		return [
			['firstResourceName:firstPrivilegeName', [
				'title'			=> 'This is first example title',
				'description'	=> 'This is first example description'
			]],
			['secondResourceName:secondPrivilegeName', [
				'title'			=> 'This is second example title',
				'description'	=> 'This is second example description'
			]],
			['thirdResourceName:thirdPrivilegeName', [
				'title'			=> 'This is third example title',
				'description'	=> 'This is third example description'
			]]
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataRegisteredUsers()
	{
		return [
			['john', '123456'],
			['jane', '123456'],
		];
	}

	public function dataGuestUsers()
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

		// Get roles model services
		$this->rolesModel = $this->container->getService('models.roles');

		// Get permissions service
		$this->permission = $this->container->getService('permissions.permissions');

		// Get presenter factory from container
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');

		// Get application user
		$this->user = $this->container->getService('user');

		// Create user authenticator
		$authenticator = new Nette\Security\SimpleAuthenticator([
			'john'	=> '123456',
			'jane'	=> '123456',
		], [
			'john'	=> [
				Permissions\Entities\IRole::ROLE_AUTHENTICATED
			],
			'jane'	=> [
				Permissions\Entities\IRole::ROLE_AUTHENTICATED,
				Permissions\Entities\IRole::ROLE_ADMINISTRATOR
			]
		]);
		$this->user->setAuthenticator($authenticator);

		// Register permissions
		foreach($this->dataPermissions() as $permission) {
			$this->permission->addPermission($permission[0], $permission[1]);
		}
	}

	/**
	 * @dataProvider dataRegisteredUsers
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function testPresenterActionAllowed($username, $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'allowed'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		// Logout user
		$this->user->logout(TRUE);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse );
		Assert::equal('Passed', $response->getSource());
	}

	/**
	 * @dataProvider dataRegisteredUsers
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function testPresenterActionAllowedRole($username, $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'allowedRole'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		// Logout user
		$this->user->logout(TRUE);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse );
		Assert::equal('Passed', $response->getSource());
	}

	/**
	 * @dataProvider dataGuestUsers
	 *
	 * @param string $username
	 *
	 * @throws Nette\Application\ForbiddenRequestException
	 */
	public function testPresenterActionNotAllowed($username)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'allowedRole'));
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
	public function testNotAllowedLoddedIn($username, $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'onlyGuest'));
		// & fire presenter & catch
		$presenter->run($request);
	}

	/**
	 * @dataProvider dataGuestUsers
	 *
	 * @param string $username
	 */
	public function testAllowedGuest($username)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'onlyGuest'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse );
		Assert::equal('Passed', $response->getSource());
	}

	/**
	 * @return Application\IPresenter
	 */
	protected function createPresenter()
	{
		// Create test presenter
		$presenter = $this->presenterFactory->createPresenter('Test');
		// Disable auto canonicalize to prevent redirection
		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

	/**
	 * @return \SystemContainer|\Nette\DI\Container
	 */
	protected function createContainer()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		Permissions\DI\PermissionsExtension::register($config);

		$config->addConfig(__DIR__ . '/files/config.neon', $config::NONE);
		$config->addConfig(__DIR__ . '/files/presenters.neon', $config::NONE);

		return $config->createContainer();
	}
}

/**
 * @Secured
 * @Secured\Resource(firstResourceName)
 * @Secured\Privilege(firstPrivilegeName)
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