<?php
namespace Yarri\Mjml\Tags;

class _Tag {

	static $componentName;

	static $endingTag;

	static $allowedAttributes = [];

	static $defaultAttributes = [];

	public $props = [
		"nonRawSiblings" => [],
	];

	function __construct($params = []){
		$params += [
			"content" => "",
			"attributes" => [],
		];

		// $this::$componentName = \String4::ToObject(get_class($this))->gsub('/^.*?([a-z]+)$/i','\1')->underscore()->replace('_','-')->lower()->toString(); // "Yarri\Mjml\Tags\MjSection" -> "mj-section"

		$this->content = $params["content"];
		$this->attributes = $params["attributes"];

		$context = new class{ };
		$context->containerWidth = 300;
		$this->context = $context;
	}

	function getContent(){
		return $this->content;
	}

	function getAttribute($name){
		$defaults = static::$defaultAttributes;
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

	function render(){
		return $this->getContent();
	}

	function renderChildren(){
		// TODO: ???
		return $this->getContent();
	}

	function suffixCssClasses($classes, $suffix){
		$out = [];
		foreach(explode(' ',$classes) as $c){
			if(!strlen($c)){ continue; }
			$out[] = "$c-$suffix";
		}
		return join(" ",$out);
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
