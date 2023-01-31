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

	function getStyles(){
		return [
			"div" => [
				'background-color' => $this->getAttribute('background-color')
			]
		];
	}

	function render(){
		return "
			<div
				{$this->htmlAttributes([
					'class' => $this->getAttribute('css-class'),
					'style' => 'div',
					// TODO: ???
					//$lang,
					//$dir
			])}
			>
				{$this->renderChildren()}
			</div>
		";
	}
}
