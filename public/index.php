<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

//Set timezone
date_default_timezone_set("UTC");

$dir = dirname(__DIR__);
$global_dir = dirname($dir);
chdir($dir);

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', $dir);
// Define path to web documents directory
defined('GLOBAL_PATH')
        || define('GLOBAL_PATH', $global_dir);

// Setup autoloading
require 'init_autoloader.php';

error_reporting(E_ALL ^ E_NOTICE);

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
