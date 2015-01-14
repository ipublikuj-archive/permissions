<?php
/**
 * Test: IPub\Permissions\Annotations
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

class AnnotationsTest extends Tester\TestCase
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
	public function testCheckUser($username, $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'user'));
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
	public function testCheckResourcePrivilege($username, $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'resourcePrivilege'));
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
	public function testCheckPermission($username, $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'permission'));
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
	public function testCheckRole($username, $password)
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Try to login user
		$this->user->login($username, $password);

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'role'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		// Logout user
		$this->user->logout(TRUE);

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

class TestPresenter extends UI\Presenter
{
	use Permissions\TPermission;

	/**
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function renderUser()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Passed'));
	}

	/**
	 * @Secured
	 * @Secured\Resource(firstResourceName)
	 * @Secured\Privilege(firstPrivilegeName)
	 */
	public function renderResourcePrivilege()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Passed'));
	}

	/**
	 * @Secured
	 * @Secured\Permission(secondResourceName:secondPrivilegeName)
	 */
	public function renderPermission()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Passed'));
	}

	/**
	 * @Secured
	 * @Secured\Role(authenticated)
	 */
	public function renderRole()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Passed'));
	}
}

\run(new AnnotationsTest());