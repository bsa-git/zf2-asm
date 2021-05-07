<?php

use Asm\Model;

return array(
    'factories' => array(
        //---- Создание таблиц баз данных -----
        'Asm\Model\Tag' => function($sm) {
            $config = $sm->get('Config');
            $disks = $config['DisksForHost'];
            $env = $config['application_env'];
            $driverArray = ($env == 'production') ? $config['Asm\db\real\hist'] : $config['Asm\db\test\hist'];
            if($env == 'production'){
                $arrHost = explode(':', $_SERVER['HTTP_HOST']);
                $host = $arrHost[0];
//                $database = ($host == 'zf2-asm.srv')? "e:{$driverArray['database']}":"d:{$driverArray['database']}";
                $database = "{$disks[$host]}{$driverArray['database']}";
                $driverArray['database'] = $database;
            }
            $dbAdapter = new Zend\Db\Adapter\Adapter($driverArray);
            $table = new Model\TagTable($dbAdapter);
            return $table;
        },
        'Asm\Model\Tag2' => function($sm) {
            $config = $sm->get('Config');
            $disks = $config['DisksForHost'];
            $env = $config['application_env'];
            $driverArray = ($env == 'production') ? $config['Asm\db\real\hist2'] : $config['Asm\db\test\hist'];
            if($env == 'production'){
                $arrHost = explode(':', $_SERVER['HTTP_HOST']);
                $host = $arrHost[0];
//                $database = ($host == 'zf2-asm.srv')? "e:{$driverArray['database']}":"d:{$driverArray['database']}";
                $database = "{$disks[$host]}{$driverArray['database']}";
                $driverArray['database'] = $database;
            }
            $dbAdapter = new Zend\Db\Adapter\Adapter($driverArray);
            $table = new Model\TagTable($dbAdapter);
            return $table;
        },        
        'Asm\Model\DataCurrent' => function($sm) {
            $config = $sm->get('Config');
            $disks = $config['DisksForHost'];
            $env = $config['application_env'];
            $driverArray = ($env == 'production') ? $config['Asm\db\real\hist'] : $config['Asm\db\test\hist'];
            if($env == 'production'){
                $arrHost = explode(':', $_SERVER['HTTP_HOST']);
                $host = $arrHost[0];
//                $database = ($host == 'zf2-asm.srv')? "e:{$driverArray['database']}":"d:{$driverArray['database']}";
                $database = "{$disks[$host]}{$driverArray['database']}";
                $driverArray['database'] = $database;
            }
            $dbAdapter = new Zend\Db\Adapter\Adapter($driverArray);
            $table = new Model\DataCurrentTable($dbAdapter);
            return $table;
        },
        'Asm\Model\DataCurrent2' => function($sm) {
            $config = $sm->get('Config');
            $disks = $config['DisksForHost'];
            $env = $config['application_env'];
            $driverArray = ($env == 'production') ? $config['Asm\db\real\hist2'] : $config['Asm\db\test\hist'];
            if($env == 'production'){
                $arrHost = explode(':', $_SERVER['HTTP_HOST']);
                $host = $arrHost[0];
//                $database = ($host == 'zf2-asm.srv')? "e:{$driverArray['database']}":"d:{$driverArray['database']}";
                $database = "{$disks[$host]}{$driverArray['database']}";
                $driverArray['database'] = $database;
            }
            $dbAdapter = new Zend\Db\Adapter\Adapter($driverArray);
            $table = new Model\DataCurrentTable($dbAdapter);
            return $table;
        },        
        'Asm\Model\ValuesCurrent' => function($sm) {
            $config = $sm->get('Config');
            $disks = $config['DisksForHost'];
            $env = $config['application_env'];
            $driverArray = ($env == 'production') ? $config['Asm\db\real\hist'] : $config['Asm\db\test\hist'];
            if($env == 'production'){
                $arrHost = explode(':', $_SERVER['HTTP_HOST']);
                $host = $arrHost[0];
//                $database = ($host == 'zf2-asm.srv')? "e:{$driverArray['database']}":"d:{$driverArray['database']}";
                $database = "{$disks[$host]}{$driverArray['database']}";
                $driverArray['database'] = $database;
            }
            $dbAdapter = new Zend\Db\Adapter\Adapter($driverArray);
            $table = new Model\ValuesCurrentTable($dbAdapter);
            return $table;
        },
        'Asm\Model\ValuesCurrent2' => function($sm) {
            $config = $sm->get('Config');
            $disks = $config['DisksForHost'];
            $env = $config['application_env'];
            $driverArray = ($env == 'production') ? $config['Asm\db\real\hist2'] : $config['Asm\db\test\hist'];
            if($env == 'production'){
                $arrHost = explode(':', $_SERVER['HTTP_HOST']);
                $host = $arrHost[0];
//                $database = ($host == 'zf2-asm.srv')? "e:{$driverArray['database']}":"d:{$driverArray['database']}";
                $database = "{$disks[$host]}{$driverArray['database']}";
                $driverArray['database'] = $database;
            }
            $dbAdapter = new Zend\Db\Adapter\Adapter($driverArray);
            $table = new Model\ValuesCurrentTable($dbAdapter);
            return $table;
        },        
    ),
);
