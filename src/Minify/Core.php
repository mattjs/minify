<?php
namespace Minify;

class Core {
	protected $minify_javascript;
	protected $minify_css;
	protected $cache_bust;
	
	protected $javascript_files = array();
	protected $css_files = array();
	
	public function __construct(Array $config) {
		$this->minify_javascript = $config['javascript'];
		$this->minify_css = $config['css'];
		$this->cache_bust = $config['cache_bust'];
	}
	
	public function add_css($file_path) {
		$this->css_files[] = $file_path;
	}
	
	public function add_js($file_path) {
		$this->javascript_files[] = $file_path;
	}
	
	public function js_html() {
		$result = '';
		if($this->minify_javascript) {
			$query = '';	
			for($i = 0; $i < count($this->javascript_files); $i++) {
				$query .= 'f[]='.$this->javascript_files[$i].'&';
			}
			$result = '<script type="text/javascript" src="/min?'.$query.'"></script>'."\n";
		} else {
			for($i = 0; $i < count($this->javascript_files); $i++) {
				$result .= '<script type="text/javascript" src="'.$this->javascript_files[$i].($this->cache_bust?'?cb='.time():'').'"></script>'."\n";
			}
		}
		return $result;
	}
	
	public function css_html() {
		$result = '';
		for($i = 0; $i < count($this->css_files); $i++) {
			$result .= '<link type="text/css" href="/css/'.$this->css_files[$i].'.css'.($this->cache_bust?'?cb='.time():'').'" rel="stylesheet" />'."\n";
		}
		return $result;
	}
}