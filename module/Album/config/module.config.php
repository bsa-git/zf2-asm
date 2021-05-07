<?php
// module/Album/config/module.config.php:

// Get the name of the current working directory
$dir = getcwd();

return array(
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
            'album' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/album[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Album\Controller',
//                        'controller' => 'Album\Controller\Album',
                        'controller' => 'Album',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
    
    //------ Конфигурация баз данных в модуле -----//
    
    'Album\db\albums' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => __DIR__ . '/../data/db/albums.db'
    ),
);
