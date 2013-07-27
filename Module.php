<?php

namespace Minify;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {	
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
		$sharedManager = $eventManager->getSharedManager();
		
		$eventManager->getSharedManager()->attach(__NAMESPACE__, 'dispatch', function($e) {
			$controller      = $e->getTarget();
			$controller->layout('layout/minify');
        }, 100);		
		
		// Create a low priority dispatch event 'postDispatch'
		$sharedManager->attach(__NAMESPACE__, 'dispatch', function($e) {
			$controller = $e->getTarget();
			if(method_exists($controller, 'postDispatch')) {
				$controller->postDispatch();
			}
		});
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
    public function getServiceConfig() {
        return array(
            'factories' => array(
            	'Minify' => function($sm) {
	            	if(!$sm->has('Minify')) {
	            		$config = $sm->get('config');
	                    $minify = new Core();
						$sm->setService('Minify', $account);
					} else {
						$minify = $sm->get('Minify');
					}
	                return $minify;
	            },
            ),
        );
    }	

    public function getAutoloaderConfig()
    {
        return array(     
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
