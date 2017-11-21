<?php

use Album\Model;

return array(
    'factories' => array(
        //---- Создание таблиц баз данных -----
        'Album\Model\Album' => function($sm) {
            $config = $sm->get('Config');
            $driverArray = $config['Album\db\albums'];
            $dbAdapter = new Zend\Db\Adapter\Adapter($driverArray);
            $table = new Model\AlbumTable($dbAdapter);
            return $table;
        },
    ),
);
