<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

// Get the name of the current working directory
$dir = getcwd();

return array(
    //--- DB Config ----
    'db' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => $dir . '/data/db/system.db'
    ),

    //--- Service Manager  Config ----
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    
    //--- Languages  Config ----
    'languages' => array(
        'ua' => array(
            'name' => 'ukrainian',
            'locale' => 'ua_UA',
        ),
        'ru' => array(
            'name' => 'russian',
            'locale' => 'ru_RU',
        ),
        'en' => array(
            'name' => 'english',
            'locale' => 'en_US',
        ),
    ),
);
