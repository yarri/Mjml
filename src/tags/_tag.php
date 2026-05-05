<?php
namespace Yarri\Mjml\Tags;

class _Tag {

	static $componentName;

	static $endingTag;

	static $allowedAttributes = [];

	static $defaultAttributes = [];

	public $props = [
		"nonRawSiblings" => 1,
		"children" => [],
	];

	public $content;

	public $attributes;

	public $context;

	function __construct($params = []){
		$params += [
			"content" => "",
			"attributes" => [],
		];

		$this->content = $params["content"];
		$this->attributes = $params["attributes"];

		$context = new \stdClass();
		$context->containerWidth = '600px';
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
			if(strpos($_styles, '.') !== false){
				// Handle dot-notation: e.g. 'carousel.div' → $styles['carousel']['div']
				[$group, $key] = explode('.', $_styles, 2);
				$styles_ar = isset($styles[$group][$key]) ? $styles[$group][$key] : [];
			}else{
				$styles_ar = isset($styles[$_styles]) ? $styles[$_styles] : [];
			}
		}else{
			$styles_ar = $_styles;
		}

		$out = [];
		foreach($styles_ar as $key => $value){
			if(is_null($value) || strlen((string)$value) === 0){ continue; }
			$out[] = "$key:$value;";
		}
		return join("",$out);
	}

	function getStyles(){
		return [];
	}

	function getChildContext(){
		return $this->context;
	}

	function getShorthandAttrValue($attribute, $direction){
		$attr_direction = $this->getAttribute("{$attribute}-{$direction}");
		$attr = $this->getAttribute($attribute);

		if(!is_null($attr_direction) && strlen((string)$attr_direction) > 0){
			return (int)$attr_direction;
		}

		if(!$attr){
			return 0;
		}

		return $this->_shorthandParser($attr, $direction);
	}

	function _shorthandParser($css_value, $direction){
		$parts = preg_split('/\s+/', trim((string)$css_value));
		switch(count($parts)){
			case 1:
				return (int)$css_value;
			case 2:
				$directions = ['top' => 0, 'bottom' => 0, 'left' => 1, 'right' => 1];
				break;
			case 3:
				$directions = ['top' => 0, 'left' => 1, 'right' => 1, 'bottom' => 2];
				break;
			default:
				$directions = ['top' => 0, 'right' => 1, 'bottom' => 2, 'left' => 3];
				break;
		}
		$idx = isset($directions[$direction]) ? $directions[$direction] : 0;
		return (int)(isset($parts[$idx]) ? $parts[$idx] : 0);
	}

	function getShorthandBorderValue($direction){
		$border_direction = $direction ? $this->getAttribute("border-{$direction}") : null;
		$border = $this->getAttribute('border');
		$border_str = (strlen((string)$border_direction) > 0 ? $border_direction : null)
					?: ($border ?: '0');
		preg_match('/(?:(?:^| )(\d+))/', $border_str, $matches);
		return isset($matches[1]) ? (int)$matches[1] : 0;
	}

	function getBoxWidths(){
		$container_width = $this->context->containerWidth;
		$parsed_width = (int)$container_width;

		$paddings = $this->getShorthandAttrValue('padding', 'right')
				  + $this->getShorthandAttrValue('padding', 'left');
		$borders = $this->getShorthandBorderValue('right')
				 + $this->getShorthandBorderValue('left');

		return [
			'totalWidth' => $parsed_width,
			'borders' => $borders,
			'paddings' => $paddings,
			'box' => $parsed_width - $paddings - $borders,
		];
	}

	function render(){
		return $this->getContent();
	}

	function renderChildren($renderer = null){
		$children = isset($this->props["children"]) ? $this->props["children"] : [];
		$out = [];
		foreach($children as $child){
			if($renderer){
				$out[] = call_user_func($renderer, $child);
			}else{
				$out[] = $child->render();
			}
		}
		return join("", $out);
	}

	function suffixCssClasses($classes, $suffix){
		$out = [];
		foreach(explode(' ',(string)$classes) as $c){
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
