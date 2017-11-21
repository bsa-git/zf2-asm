<?php

use Application\Model;
use Application\Service;

return array(
    'factories' => array(
        //---- Создание таблиц баз данных -----
        'Application\Model\System' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $table = new Model\AlbumTable($dbAdapter);
            return $table;
        },
        //---- Создание Service\TestService -----
        'Application\Service\TestService' => function($sm) {
            $testService = new Service\TestService();
            return $testService;
        },
    ),
);
