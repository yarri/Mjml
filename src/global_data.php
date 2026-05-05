<?php
namespace Yarri\Mjml;

class GlobalData {

	public $mediaQueries = [];
	public $backgroundColor = '';
	public $breakpoint = '480px';
	public $title = '';
	public $preview = '';
	public $fonts = [];
	public $style = [];
	public $headStyle = []; // map[componentName => callable($breakpoint)] — deduplicated per component type
	public $componentsHeadStyle = []; // array of callables — per-instance (carousel, etc.)
	public $headRaw = [];
	public $defaultAttributes = [];

	function addHeadStyle($componentName, $callable){
		$this->headStyle[$componentName] = $callable;
	}

	function addComponentHeadStyle($callable){
		$this->componentsHeadStyle[] = $callable;
	}

	function addMediaQuery($className, $parsedWidth, $unit){
		$this->mediaQueries[$className] = "{ width:{$parsedWidth}{$unit} !important; max-width: {$parsedWidth}{$unit}; }";
	}

	function setBackgroundColor($color){
		if(!is_null($color) && strlen((string)$color) > 0){
			$this->backgroundColor = $color;
		}
	}
}
