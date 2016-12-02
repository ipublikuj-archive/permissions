# Quickstart

Simple permission checker for [Nette Framework](http://nette.org/)

## Installation

The best way to install ipub/permissions is using  [Composer](http://getcomposer.org/):

```sh
$ composer require ipub/permissions:@dev
```

After that you have to register extension in config.neon.

```neon
extensions:
	permission: IPub\Permissions\DI\PermissionsExtension
```

Package contains trait, which you will have to use in presenter to override default **checkRequirements** method. This works only for PHP 5.4+, for older version you can simply copy trait content and paste it into class where you want to use it.

```php
<?php

class BasePresenter extends Nette\Application\UI\Presenter
{

	use IPub\Permissions\TPermission;

}
```

## About ACL in Nette

Nette ACL system brings some terminology you should know before continuing. First there are resources that one (a role) wants to access (privilege). This forms a permission.
Example is the best teacher:

**resources** - We can imagine them as a places where user can go. Eg. intranet, salesModule, ordersModule, etc.

**privileges** - We can image them as actions what user can do. Eg. access, create, delete

**permission** - Is combination of *resource* and *privilege*. So you are defining what you are able to do on current place. Eg. *intranet:access* mean that with this permission you can access the intranet part.

**roles** - Are some groups where user could be assigned and therefore have defined rights. Eg. administrator, guest, authenticated, employee, sales, manager

*resources* and *roles* can inherit from each other and create hierarchies:

**Resources**
```
    customerArea
    └ intranet
      ├ salesModule
      └ ordersModule
        └ invoices
        └ products
```

**Roles(Permissions)**
```
    administrator(all:all)
    guest(none:none)
    └ authenticated(customerArea:all)
      └ employee(intranet:all)
        ├ sales(salesModule:all, ordersModule:read)
        └ manager(ordersModule:all)
          └ shop-owner(invoices:all, products:all)
```

If there is a permission (combination of `resource`, `role` and `privilege`) registered, this inherits down, in our little example `employee` can `access` the `customerArea` because is inheriting this permission from `authenticated`. Role `manager` 
can access `ordersModule`, `intranet` and `customerArea` because is inheriting this permission from `employee` and `authenticated`.

More on this can be found in [access control](https://doc.nette.org/en/2.3/access-control) chapter of [Nette Framework](http://nette.org/) [documentation](http://doc.nette.org/).

## Configuration

### Manual registration

#### Creating permissions & resources

At first, you have to feed up permission service with resources and permissions. This extension has default resources and permissions providers so you could use them as basic part.
So if you want to use them, you have to feed them with data:

```php
<?php

class MyResourcesProvider extends \IPub\Permissions\Providers\ResourcesProvider
{
    public function __construct()
    {
        // Registering resources
        $customerArea = $this->addResource('customerArea');

        $intranet = $this->addResource('intranet', $customerArea);

        $salesModule = $this->addResource('salesModule', $intranet);
        $ordersModule = $this->addResource('ordersModule', $intranet);
        
        $invoices = $this->addResource('invoices', $ordersModule);
        $products = $this->addResource('products', $ordersModule);

        // ... more resources definitions
    }
}
```

```php
<?php

class MyPermissionsProvider extends \IPub\Permissions\Providers\PermissionsProvider
{
    public function __construct(\IPub\Permissions\Providers\IResourcesProvider $resourcesProvider)
    {
        // Registering permissions
        $this->addPermission($resourcesProvider->getResource('customerArea'), \Nette\Security\IAuhtorizator::ALL);
        $this->addPermission($resourcesProvider->getResource('intranet'), \Nette\Security\IAuhtorizator::ALL');
        $this->addPermission($resourcesProvider->getResource('salesModule'), \Nette\Security\IAuhtorizator::ALL');
        $this->addPermission($resourcesProvider->getResource('ordersModule'), \Nette\Security\IAuhtorizator::ALL');
        $this->addPermission($resourcesProvider->getResource('ordersModule'), 'read');
        $this->addPermission($resourcesProvider->getResource('invoices'), \Nette\Security\IAuhtorizator::ALL');
        $this->addPermission($resourcesProvider->getResource('products'), \Nette\Security\IAuhtorizator::ALL', function($acl, $role, $resource, $privilege) {
            // ...code of permission assertion
        });

        // ... more permissions definitions
    }

}
```

Now just register both providers like any other services:

```neon
permissions:
    providers:
        resources: FALSE    // We have to disable automatic service registration
        permissions: FALSE  // We have to disable automatic service registration

services:
    - \YourNamespace\MyResourcesProvider
    - \YourNamespace\MyPermissionsProvider
```

or you can combine them together:

```neon
permissions:
    providers:
        resources: \YourNamespace\MyResourcesProvider       // Override default extension service 
        permissions: \YourNamespace\MyPermissionsProvider   // Override default extension service
```

#### Creating roles

As second step, you have to feed up permission service with roles. Permission service is waiting for some RolesProvider which implement IPub\Permissions\Providers\IRolesProvider interface.
If you don't want to create your own service, this extension is registering its own basic roles provider, similar to resources and permissions providers above.

```php
<?php

class MyRolesProvider extends \IPub\Permissions\Providers\RolesProvider
{
    public function __construct(\IPub\Permissions\Providers\IPermissionsProvider $permissionsProvider)
    {
        // Registering roles

		$this->addRole(\IPub\Permissions\Entities\IRole::ROLE_ADMINISTRATOR);
		$this->addRole(\IPub\Permissions\Entities\IRole::ROLE_ANONYMOUS);
		$this->addRole(\IPub\Permissions\Entities\IRole::ROLE_AUTHENTICATED, $this->getRole(\IPub\Permissions\Entities\IRole::ROLE_ANONYMOUS), $permissionsProvider->getPermission('customerArea:'));

		$this->addRole('employee', $this->getRole(\IPub\Permissions\Entities\IRole::ROLE_AUTHENTICATED), [
            $permissionsProvider->getPermission('intranet:'),
		]);
		$this->addRole('sales', $this->getRole('employee'), [
			$permissionsProvider->getPermission('salesModule:'),
			$permissionsProvider->getPermission('ordersModule:read'),
		]);
		$this->addRole('manager', $this->getRole('employee'), [
			$permissionsProvider->getPermission('ordersModule:'),
		]);

        // ... more roles definitions
    }

}
```

Don't forget to register your roles provider:

```neon
permissions:
    providers:
        roles: FALSE    // We have to disable automatic service registration

services:
	- \YourNamespace\MyRolesProvider
```

or you can combine them together:

```neon
permissions:
    providers:
        roles: \YourNamespace\MyRolesProvider   // Override default extension service 
```

Now your'e set!

### Automatic registration

If you don't want to create your own providers services and use little bit automation, you can use extension providers with automatic services registration and fill.

#### Creating resources & privileges

Once you have created your roles, you can create resources and his privileges. For this operation is available IPermissionProvider. So simply implement this interface into you extension

```php
<?php

class YourSuperCoolModule extends Nette\DI\CompilerExtension implements IPub\Permissions\DI\IPermissionsProvider
{
	/**
	 * Return array of module permissions
	 *
	 * @return array
	 */
	public function getPermissions()
	{
		return [
			'someResourceName: somePrivilegeName' => [
				'title' => 'this part is optional and can be used for editing purposes, etc.',
				'description' => 'this part is optional and can be used for editing purposes, etc.'
			],
			'someOtherResourceName:' => [
				'title' => 'this part is optional and can be used for editing purposes, etc.',
				'description' => 'this part is optional and can be used for editing purposes, etc.'
			],
		]
	}
}
```

So as you can see, there is special method **getPermissions** and this method only return an array of all permission which you want to register. Extension will parse this values and register all resources and permissions automatically.

## Usage

Library provide a PHP trait, which enables pleasant quering Nette ACL system you've just configured. Please note that traits are available from PHP 5.4, for older versions of PHP you must copy/paste trait contents. This trait is effective only in 
presenter(s).

```php
class BasePresenter extends Nette\Application\UI\Presenter
{
    use \IPub\Permissions\TPermission;
}
```

### Using in annotations

This extension provide a variable annotation checker. So you can secure each presenter or presenter action.

```php
<?php

/**
 * @Secured
 * @Secured\User(loggedIn)
 * @Secured\Resource(NameOfResource)
 * @Secured\Privilege(NameOfPrivilege)
 * @Secured\Permission(NameOfResource: NameOfPrivilege)
 * @Secured\Role(NameOfRole)
 */
class ArticlesPresenter extends \Nette\Application\UI\Presenter
{

}
```

As you can see, annotation is prefixed with **Secured** string.

#### Annotation @Secured

You have to use this annotation every time, without it will be skipped permission check.

#### Annotation @Secured\User

This annotation is expecting one of two values **loggedIn** or **guest**. When you use loggedIn, and user is not logged forbidden exception is raised. For guest it is reversed.

#### Annotation @Secured\Resource

In this annotation you can specify **one** resource.

#### Annotation @Secured\Privilege

In this annotation you can specify all privileges. In combination with **@Secured\Resource** annotation it will have more power to check user access rights.

#### Annotation @Secured\Permission

This annotation is expecting permission string *NameOfResource: NameOfPrivilege*. This annotation allows multiple definitions.

#### Annotation @Secured\Role

And in this annotation you can specify all user roles which are allowed to have access. This annotation allows multiple definitions.

### Using in presenters, components, models, etc.

Everywhere you want to check user rights to some action, you just create a simple call:

```php
<?php

$user->isAllowed('resource', 'privilege');
```

and if user has access to this combination, you will receive *TRUE* value

### Using in Latte

In latte you can use two special macros.

```html
<div class="some class">
	<p>
		This text is for everyone....
	</p>
	{ifAllowed resource => 'system', privilege => 'manage user permissions'}
	<p>
		But this one is only for special persons....
	</p>
	{/ifAllowed}
</div>
```

Macro **ifAllowed** is very similar to annotations definition. You can use here one or all of available parameters: user, resource, privilege, permission or role.

This macro can be also used as **n:** macro:

```html
<div class="some class">
	<p>
		This text is for everyone....
	</p>
	<p n:ifAllowed resource => 'system', privilege => 'manage user permissions'>
		But this one is only for special persons....
	</p>
</div>
```

And second special macro is for links:

```html
<a n:allowedHref="Settings:" class="some class">
	Link text...
</a>
```

Macro **n:allowedHref** is expecting only valid link and in case user doesn't have access to this link, link is displayed.

### Redirect to login page

If user is not logged-in and tries to access secured resource a default action is throwing the&nbsp;`\Nette\Application\ForbiddenRequestException`. However if you configure so called `redirectUrl`, request will be redirected to this url (eg. login 
page) when this situation occurs.

Also all parameters of the original request will be stored. That way you are able to restore original request and be redirected to secured resource after successful login. To configure `redirectUrl` add this to your configuration:

```
permissions:
	redirectUrl: 'Login:default'
```

To restore the original request prepare persistent param `backlink` in the presenter and use it in login procedure (callback)

```php
class LoginPresenter extends \Nette\Application\UI\Presenter
{
    /**
     * @persistent
     */
    public $backlink;

    public function processLoginForm($form)
    {
        $this->getUser()->login($form->getValues());
        $this->restoreRequest($this->backlink);
        $this->redirect('Admin:default');
    }
}
```
