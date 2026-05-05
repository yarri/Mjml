<?php
namespace Yarri\Mjml\Tags;

class MjBody extends _Tag {

	static $componentName = "mj-body";

	static $allowedAttributes = [
		'width' => 'unit(px)',
		'background-color' => 'color'
	];

	static $defaultAttributes = [
		'width' => '600px'
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

		return "
			<div
				{$this->htmlAttributes([
					'class' => $this->getAttribute('css-class'),
					'style' => 'div',
			])}
			>
				{$this->renderChildren()}
			</div>
		";
	}
}
