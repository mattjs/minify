<?php
return array(
    'router' => array(
        'routes' => array(        
            'min' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/min',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Minify\Controller',
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
    'controllers' => array(
        'invokables' => array(
            'Minify\Controller\Index' => 'Minify\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'template_path_stack' => array(
           'minify' => __DIR__ . '/../view',
        ),
        'layout' => 'layout/minify',
    ),
    'minify' => array(
		'javascript' => true,
		'css' => true,
		'cache_bust' => true
	)
);
