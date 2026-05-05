<?php
namespace Yarri\Mjml\Tags;

class MjTable extends _Tag {

	static $componentName = 'mj-table';

	static $allowedAttributes = [
		'align' => 'enum(left,right,center)',
		'border' => 'string',
		'cellpadding' => 'integer',
		'cellspacing' => 'integer',
		'container-background-color' => 'color',
		'color' => 'color',
		'font-family' => 'string',
		'font-size' => 'unit(px)',
		'font-weight' => 'string',
		'line-height' => 'unit(px,%,)',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'padding' => 'unit(px,%){1,4}',
		'role' => 'enum(none,presentation)',
		'table-layout' => 'enum(auto,fixed,initial,inherit)',
		'vertical-align' => 'enum(top,bottom,middle)',
		'width' => 'unit(px,%)',
	];

	static $defaultAttributes = [
		'align' => 'left',
		'border' => 'none',
		'cellpadding' => '0',
		'cellspacing' => '0',
		'color' => '#000000',
		'font-family' => 'Ubuntu, Helvetica, Arial, sans-serif',
		'font-size' => '13px',
		'line-height' => '22px',
		'padding' => '10px 25px',
		'table-layout' => 'auto',
		'width' => '100%',
	];

	function getStyles(){
		return [
			'table' => [
				'color' => $this->getAttribute('color'),
				'font-family' => $this->getAttribute('font-family'),
				'font-size' => $this->getAttribute('font-size'),
				'line-height' => $this->getAttribute('line-height'),
				'table-layout' => $this->getAttribute('table-layout'),
				'width' => $this->getAttribute('width'),
				'border' => $this->getAttribute('border'),
			],
		];
	}

	function getWidth(){
		$width = $this->getAttribute('width');
		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($width, ['parseFloatToInt' => false]);
		return $ar['unit'] === '%' ? $width : $ar['parsedWidth'];
	}

	function render(){
		$tableAttrs = $this->htmlAttributes([
			'cellpadding' => $this->getAttribute('cellpadding'),
			'cellspacing' => $this->getAttribute('cellspacing'),
			'role' => $this->getAttribute('role'),
			'width' => $this->getWidth(),
			'border' => '0',
			'style' => 'table',
		]);
		return "<table {$tableAttrs}>{$this->getContent()}</table>";
	}
}
