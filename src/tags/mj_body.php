<?php
namespace Yarri\Mjml\Tags;

class MjBody extends _Tag {

	static $componentName = "mj-body";

	static $allowedAttributes = [
		'width' => 'unit(px)',
		'background-color' => 'color',
		'lang' => 'string',
		'dir' => 'enum(ltr,rtl,auto)'
	];

	static $defaultAttributes = [
		'width' => '600px',
		'lang' => 'und',
		'dir' => 'auto'
	];

	function getChildContext(){
		$context = clone $this->context;
		$context->containerWidth = $this->getAttribute('width');
		return $context;
	}

	function getStyles(){
		return [
			"div" => [
				'background-color' => $this->getAttribute('background-color')
			]
		];
	}

	function render(){
		if(isset($this->context->globalData)){
			$this->context->globalData->setBackgroundColor($this->getAttribute('background-color'));
		}

		$title = isset($this->context->globalData) ? $this->context->globalData->title : null;

		$attrs = [];
		if($title){ $attrs['aria-label'] = $title; }
		$attrs['aria-roledescription'] = 'email';
		$attrs['class'] = $this->getAttribute('css-class');
		$attrs['style'] = 'div';
		$attrs['role'] = 'article';
		$attrs['lang'] = $this->getAttribute('lang');
		$attrs['dir'] = $this->getAttribute('dir');

		return "
			<div {$this->htmlAttributes($attrs)}>
				{$this->renderChildren()}
			</div>
		";
	}
}
