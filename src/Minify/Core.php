<?php
namespace Minify;

class Core {
	protected $minify_javascript;
	protected $minify_css;
	protected $cache_bust;
	
	protected $javascript = array();
	protected $css = array();
	
	public function __construct(Array $config) {
		$this->minify_javascript = $config['javascript'];
		$this->minify_css = $config['css'];
		$this->cache_bust = $config['cache_bust'];
	}
	
	public function add_css($file_path) {
		$this->css[] = $file_path;
	}
	
	public function add_js($file_path, $minify=true) {
		$this->javascript[] = new Javascript($file_path, $minify);
	}
	
	public function css_html() {
		$result = '';
		for($i = 0; $i < count($this->css); $i++) {
			$result .= '<link type="text/css" href="'.$this->css[$i].($this->cache_bust?'?cb='.time():'').'" rel="stylesheet" />'."\n";
		}
		return $result;
	}
	
	protected function _js($file) {
		return '<script type="text/javascript" src="'.$file.($this->cache_bust?'?cb='.time():'').'"></script>'."\n";
	}	
	
	public function js_html() {
		$result = '';
		if($this->minify_javascript) {
			$minify_query = '';	
			for($i = 0; $i < count($this->javascript); $i++) {
				if($this->javascript[$i]->minify) {
					$minify_query .= 'f[]='.$this->javascript[$i]->file_path.'&';
				} else {
					$result .= $this->_js($this->javascript[$i]->file_path); // Non min come before min always
				}
			}
			$result = $this->_js('/min?'.$minify_query);
		} else {
			for($i = 0; $i < count($this->javascript); $i++) {
				$result .= $this->_js($this->javascript[$i]->file_path);
			}
		}
		return $result;
	}	
}