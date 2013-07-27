<?php
namespace Minify;

class Minify {
	const CLOSURE_API_URL = 'http://closure-compiler.appspot.com/compile';
	const SIMPLE_DIR = '/module/Min/files/simple';
	const COMPLEX_DIR = '/module/Min/files/complex';
	
	private $params = array();
	
	public function __construct() {
		$this->params['output_format'] = 'text';
   	 	$this->params['output_info'] = 'compiled_code';
		$this->params['externs_url'] = 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js';
	}
	
	public function set_level($level) {
		switch($level) {
			case 'simple':
				$this->params['compilation_level'] = 'SIMPLE_OPTIMIZATIONS';
				
				$this->directory = getcwd().self::SIMPLE_DIR;
				if(!is_dir($this->directory)) {
					die('Please make a directory at '.self::SIMPLE_DIR);
				}
			break;
			case 'complex':
				// have to combine as one file to do advanced
				$this->params['compilation_level'] = 'ADVANCED_OPTIMIZATIONS';
				
				$this->directory = getcwd().self::COMPLEX_DIR;
				if(!is_dir($this->directory)) {
					die('Please make a directory at '.self::COMPLEX_DIR);
				}
			break;
		}
	}
	
	public function make($file_names) {
		$data = false;
		
		if($this->valid($file_names)) {	
			// Set level based upon number of files
			if(is_array($file_names) && count($file_names) > 1) {
				$data = $this->make_complex($file_names);
			} else {
				$data = $this->make_simple(is_array($file_names)?$file_names[0]:$file_names);
			}
		} else {
			die('Invalid');
		}
		
		return $data;
		
		
		$file = array();
		// remove first / if there
		if($filename[0] == '/') {
			$filename = substr($filename, 0, -1);
		}
		
		$file_src = getcwd().'/public/'.$filename;
		
		if(file_exists($file_src)) {
			$min_src = $this->directory.'/'.$filename;
		
			if(file_exists($min_src)) { // Minified exists
				//  return file
				$file = json_decode(file_get_contents($min_src), true);
				// If file has changed, recompile
				if(md5_file($file_src) != $file['fingerprint']) {
					unset($min_src);
					$file = $this->_make($filename);
				}
			} else {
				$file = $this->_make($filename);
			}
		} else {
			die('404');
		}
		
		return $file;
	}
	/**
	 *  Allow [\w\d\/\.\-]* to be passed as a file name
	 */
	public function valid($file_names) {
		$valid = true;
		if(is_array($file_names)) {
			foreach($file_names as $name) {
				if(!$this->_valid($name)) {
					$valid = false;
					break;
				}
			}
		} else {
			$valid = $this->_valid($file_names);
		}
		return $valid;
	}
	/**
	 * @todo dont allow folder navigation like ../ to
	 * contain to public directory
	 */
	public function _valid($file_name) {
		return preg_match('/[\w\d\/\.\-]*/', $file_name);// && !preg_match('/[(\/\.\.)(\.\.\/)]/', $file_name);
	}
	
	public function make_complex($file_names) {
		$this->set_level('complex');
		
		$data = array();
		$files = array();
		
		$complex_string = '';
		$files_exist = true;
		
		for($i = 0; $i < count($file_names); $i++) {
			$file_names[$i] = $this->remove_leading_slash($file_names[$i]);
			$complex_string .= $file_names[$i];
			
			$file = array();
			
			$file['src'] = $file_names[$i];
			$file['fingerprint'] = @md5_file(getcwd().'/public/'.$file_names[$i]);
			
			if(!$file['fingerprint']) {
				$files_exist = false;
				break;
			}
			
			$files[] = $file;
		}
		
		if($files_exist) {
			$complex_name = md5($complex_string);
			
			$min_src = $this->directory.'/'.$complex_name;
			
			$file_contents = @file_get_contents($min_src);
			
			if($file_contents) {
				$change = false;
				//  return file
				$data = json_decode($file_contents, true);
				// check for changes
				for($i = 0; $i < count($data['files']); $i++) {
					if($files[$i]['fingerprint'] != $data['files'][$i]['fingerprint']) {
						$change = true;
						break;
					}
				}
				
				if($change) {
					unset($min_src);
					$data = $this->_make_complex($files,$complex_name);
				}
			} else {
				$data = $this->_make_complex($files,$complex_name);
			}
		} else {
			$data = $this->_404();
		}
		
		return $data;
	}
	
	public function _make_complex($files, $name) {
		$code = '';
		for($i = 0; $i < count($files); $i++) {
			$code .= file_get_contents(getcwd().'/public/'.$files[$i]['src']);
		}
		
		$file = array();
		
		$file['files'] = $files;
		$file['data'] = $this->compile($code);;		
		$file['size'] = strlen($file['data']);
		$file['content_type'] = 'application/javascript';
		
		$this->save($file, $name);
		
		return $file;
	}
	
	public function make_simple($file_name) {
		$data = array();
		
		$this->set_level('simple');
		$file_name = $this->remove_leading_slash($file_name);

		$min_src = $this->directory.'/'.$file_name;
		$min_contents = @file_get_contents($min_src);
		
		if(file_exists($min_contents)) { // Minified exists
			//  return file
			$data = json_decode($min_contents, true);
			$fingerprint = @md5_file($file_name);
			// If file has changed, recompile
			if(!$fingerprint) {
				if($fingerprint != $data['fingerprint']) {
					unset($min_src);
					$data = $this->_make_simple($file_name);
				}
			} else {
				$data = $this->_404();
			}	
		} else if(file_exists(getcwd().'/public/'.$file_name)) {
			$data = $this->_make_simple($file_name);
		} else {
			$data = $this->_404();
		}
		
		return $data;
	}
	
	public function _404() {
		$data = array();
		$data['content_type'] = 'text/html';
		$data['data'] = '404 File Not Found';
		$data['size'] = strlen($file['data']);
		return $data;
	}

	public function _make_simple($file_name) {
		$file = array();
		$file_src = getcwd().'/public/'.$file_name;
		$file['fingerprint'] = md5_file($file_src);
		$file['data'] = $this->compile(file_get_contents($file_src));
		$file['size'] = strlen($file['data']);
		$file['content_type'] = 'application/javascript';
		$this->save($file, $file_name);
		return $file;
	}
	
	public function remove_leading_slash($name) {
		if($name[0] == '/') {
			$name = substr($name, 1);
		}
		return $name;
	}
	
	public function save($file,$path) {
		$folders = explode('/',$path);
		array_pop($folders); // pop off name
		
		if(count($folders) && !is_dir($this->directory.'/'.implode('/', $folders))) {
			for($i = 0; $i < count($folders); $i++) {
				$dir = $this->directory.'/'.implode('/', array_slice($folders, 0, $i+1));
				if(!is_dir($dir)) {
					if(!mkdir($dir, 0755)) {
						die('error making directory');	
					}
				}
			}
		}
		
		$handle = @fopen($this->directory.'/'.$path, 'w');
		
		if($handle) {
			fwrite($handle, json_encode($file));
			fclose($handle);
		} else {
			die('error creating the file');
		}
	}
	
	public function compile($code) {
		$this->params['js_code'] = $code;
		return $this->post(self::CLOSURE_API_URL, $this->params);
	}
	
	protected function post($url, $data) {
		// Cant leave as array, for some reason has to go with google only
		// accepting the x-www-form-urlencoded header
		$post_string = '';
		foreach($data as $key => $value) {
			$post_string.=$key.'='.urlencode($value).'&';
		}
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		
		$response = curl_exec($ch);
		
		// If no response return error as array
		if($response === false) {
		    $response = array('error' => array('details' => curl_error($ch) ));
		}
		
		curl_close($ch);
		
		return $response;
	}
}