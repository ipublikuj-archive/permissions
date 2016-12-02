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
    intranet
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

### Creating roles

At first, you have to feed up permission service with roles. Permission service is waiting for some RolesProvider which implement IPub\Permissions\Providers\IRolesProvider interface. This model have to return an array of roles and this roles has to implement IPub\Permissions\Entities\IRole.

Some basic role class is created in IPub\Permissions\Entities. So your role class can be extended by this class IPub\Permissions\Entities\Role

### Creating resources & privileges

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
				'title' => 'this part is optional and can be used pro editing purposes, etc.',
				'description' => 'this part is optional and can be used pro editing purposes, etc.'
			],
		]
	}
}
```

So as you can see, there is special method **getPermissions** and this method only return an array of all permission which you want to register.

In case you need to register permissions (resources & privileges) manually, you can do it via permission service in presenter. This service is imported by **TPermission** trait.

```php
<?php

class BasePresenter extends Nette\Application\UI\Presenter
{

	protected function startup()
	{
		parent::startup();

		// Load all your permissions from model, file, etc.
		$permissions = $this->someYourModelEtc->getPermissions();

		// And assign them to the service
		foreach($permissions as $permission => $details) {
			$this->permission->addPermission($permission, $details)
		}
	}

}
```

In the variable **$permission** can be string with special delimiter (:), something like this:  'NameOfResource:NameOfPrivilege' or it could be an array which contain two keys - resource and privilege or it could be an object which implement IPub\Permissions\Entities\IPermission

### Connecting roles & resources & privileges

Each role has to contain special method **hasPermission**. If you create a call on your role $role->hasPermission('NameOfResource:NameOfPrivilege') and it returns TRUE, that means this role has access to this resource and privilege, in other case not. So permission service create this connection automatically when you create resource and privilege.

## Usage

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

You have to use this annotation every time, without it will be skipper permission check.

#### Annotation @Secured\User

This annotation is expecting one of two values **loggedIn** or **guest**. When you use loggedIn, and user is not logged forbidden exception is raised. For guest it is reversed.

#### Annotation @Secured\Resource

In this annotation you can specify one resource.

#### Annotation @Secured\Privilege

In this annotation you can specify all privileges. In combination with **@Secured\Resource** annotation it will have more power to check user access rights.

#### Annotation @Secured\Permission

This annotation is expecting permission string *NameOfResource: NameOfPrivilege*.

#### Annotation @Secured\Role

And in this annotation you can specify all user roles which are allowed to have access.

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
	{/ifAllowed}
</div>
```

And second special macro is for links:

```html
<a n:allowedHref="Settings:" class="some class">
	Link text...
</a>
```

Macro **n:allowedHref** is expecting only valid link and in case user doesn't have access to this link, link is displayed.
