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
                /*'may_terminate' => true,
                'child_routes' => array(
                	'default' => array(
	                	'type' => 'Segment',
						'options' => array(
							'route' => '/:type/:hash',
							'constraints' => array(
								'type' => 'js|css',
								'hash' => '[\w\d\.\-]+'
							),
						)
					)
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
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
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
