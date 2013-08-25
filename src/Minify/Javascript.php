<?php
namespace Minify;

class Javascript {
	protected $file_path;
	protected $minify;
	
	public function __construct($file_path, $minify) {
		$this->file_path = $file_path;
		$this->minify = $minify;
	}
}