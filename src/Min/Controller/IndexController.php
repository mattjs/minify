<?php

namespace Min\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Min\Model\Minify;

class IndexController extends AbstractActionController {
	
    public function indexAction() {
		$files = $this->params()->fromQuery('f');

		$view = array();
		
		if($files) {
			$minify = new Minify();
			$view['file'] = $minify->make($files);
		} else {
			$view['file']['content_type'] = 'text/html';
			$view['file']['data'] = 'No file provided';
			$view['file']['size'] = strlen($view['file']['data']);
		}
		
		return new ViewModel($view);
    }
}
