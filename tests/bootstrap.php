<?php
\define('PHPUNIT_RUN', 1);

$configDir = \getenv('CONFIG_DIR');
if ($configDir) {
	\define('PHPUNIT_CONFIG_DIR', $configDir);
}

require_once __DIR__ . '/../lib/base.php';

// especially with code coverage it will require some more time
\set_time_limit(0);

\OC::$composerAutoloader->addPsr4('Test\\', OC::$SERVERROOT . '/tests/lib/', true);
\OC::$composerAutoloader->addPsr4('Tests\\', OC::$SERVERROOT . '/tests/', true);

// load all enabled apps
\OC_App::loadApps();

PHPUnit_Framework_Error_Deprecated::$enabled = false;

OC_Hook::clear();