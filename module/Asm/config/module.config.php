<?php

// module/Asm/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Asm\Controller\Asm' => 'Asm\Controller\AsmController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
            'asm' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/asm[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Asm\Controller',
                        'controller' => 'Asm',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'asm' => __DIR__ . '/../view',
        ),
    ),
    
    
    //------ Конфигурация баз данных в модуле -----//
    
    'Asm\db\real\hist' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => '/www_m5/db_data/hist_m5.db'
    ),
    'Asm\db\real\hist2' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => '/www_m5/db_data2/hist_m5.db'
    ),
    'Asm\db\test\hist' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => GLOBAL_PATH . '/cli-mon-rsu/test/db/hist_m5.db' 
    ),
);
