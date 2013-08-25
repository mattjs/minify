<?php
namespace Minify;

class Javascript {
	public $file_path;
	public $minify;
	
	public function __construct($file_path, $minify) {
		$this->file_path = $file_path;
		$this->minify = $minify;
	}
}