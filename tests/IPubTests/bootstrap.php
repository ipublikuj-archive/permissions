<?php

declare(strict_types = 1);

define('DS', DIRECTORY_SEPARATOR);

require __DIR__ . DS . '..' . DS . '..' . DS . 'vendor' . DS . 'autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

// Create temporary directory
define('TEMP_DIR', __DIR__ . DS . '..' . DS . 'tmp' . DS . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
Tester\Helpers::purge(TEMP_DIR);
\Tracy\Debugger::$logDirectory = TEMP_DIR;

function id($val)
{
	return $val;
}

function run(Tester\TestCase $testCase)
{
	$testCase->run();
}
