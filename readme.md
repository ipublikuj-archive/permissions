# Permissions

[![Build Status](https://img.shields.io/travis/iPublikuj/permissions.svg?style=flat-square)](https://travis-ci.org/iPublikuj/permissions)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/iPublikuj/permissions.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/permissions/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/iPublikuj/permissions.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/permissions/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/permissions.svg?style=flat-square)](https://packagist.org/packages/ipub/permissions)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/permissions.svg?style=flat-square)](https://packagist.org/packages/ipub/permissions)
[![License](https://img.shields.io/packagist/l/ipub/permissions.svg?style=flat-square)](https://packagist.org/packages/ipub/permissions)

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

## Documentation

Learn how to control access to your application in [documentation](https://github.com/iPublikuj/permissions/blob/master/docs/en/index.md).

***
Homepage [http://www.ipublikuj.eu](http://www.ipublikuj.eu) and repository [http://github.com/iPublikuj/permissions](http://github.com/iPublikuj/permissions).
