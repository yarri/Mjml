<?php
namespace Yarri\Mjml\Tags;

class _Tag {

	var $endingTag = true;

	var $allowedAttributes = [];

	var $defaultAttributes = [];

	function __construct($params = []){
		$params += [
			"content" => "",
			"attributes" => [],
		];

		$this->content = $params["content"];
		$this->attributes = $params["attributes"];
	}

	function getContent(){
		return $this->content;
	}

	function getAttribute($name){
		$defaults = $this->defaultAttributes;
		$attribute = isset($defaults[$name]) ? $defaults[$name] : null;
		if(array_key_exists($name,$this->attributes)){
			$attribute = $this->attributes[$name];
		}
		return $attribute;
	}

	function htmlAttributes($attributes){
		$out = [];
		foreach($attributes as $key => $value){
			if(is_null($value)){ continue; }
			if($key === "style"){
				$out[] = \h($key).'="'.\h($this->styles($value)).'"';
				continue;
			}
			$out[] = \h($key).'="'.\h($value).'"';
		}
		return join(" ",$out);
	}

	function styles($_styles){
		$styles = $this->getStyles();

		if(is_string($_styles)){
			$styles_ar = $styles[$_styles];
		}else{
			$styles_ar = $_styles;
		}

		$out = [];
		foreach($styles_ar as $key => $value){
			if(strlen("$value")===0){ continue; }
			$out[] = "$key:$value;";
		}
		return join("",$out);
	}

	function renderContent(){
		return "";
	}

	function render(){
		return "";
	}

	function _trimHtml($text){
		$text = trim($text);
		$out = [];
		foreach(explode("\n",$text) as $line){
			$out[] = trim($line);
		}
		return join("\n",$out);
	}
}
