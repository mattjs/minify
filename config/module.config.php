<?php
return array(
    'router' => array(
        'routes' => array(        
            'min' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/min',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Min\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                /*'child_routes' => array(
                	'query' => array(
                		'type' => 'Query',
                		'options' => array(
                			'constraints' => array(
								'f[]' => '[\w\d\/\.\-]*'
							)
			           	)
			        ),
                ),*/
            ),             
        ),
    ),
    'service_manager' => array(
    ),
    'controllers' => array(
        'invokables' => array(
            'Min\Controller\Index' => 'Min\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/min'           => __DIR__ . '/../view/layout/min.phtml',
            'min/index/index' => __DIR__ . '/../view/min/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
           'min' => __DIR__ . '/../view',
        ),
    ),
);
