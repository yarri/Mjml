<?php
namespace Yarri\Mjml\Tags;

class MjGroup extends _Tag {

	static $componentName = 'mj-group';

	static $allowedAttributes = [
		'background-color' => 'color',
		'direction' => 'enum(ltr,rtl)',
		'vertical-align' => 'enum(top,bottom,middle)',
		'width' => 'unit(px,%)',
	];

	static $defaultAttributes = [
		'direction' => 'ltr',
	];

	function getChildContext(){
		$parentWidth = $this->context->containerWidth;
		$nonRawSiblings = max(1, (int)$this->props["nonRawSiblings"]);
		$paddingSize = $this->getShorthandAttrValue('padding', 'left')
					 + $this->getShorthandAttrValue('padding', 'right');

		$containerWidth = $this->getAttribute('width')
			?: ((float)$parentWidth / $nonRawSiblings) . 'px';

		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($containerWidth, ['parseFloatToInt' => false]);
		$unit = $ar['unit'];
		$parsedWidth = $ar['parsedWidth'];

		if($unit === '%'){
			$containerWidth = ((float)$parentWidth * $parsedWidth / 100 - $paddingSize) . 'px';
		}else{
			$containerWidth = ($parsedWidth - $paddingSize) . 'px';
		}

		$context = clone $this->context;
		$context->containerWidth = $containerWidth;
		return $context;
	}

	function getParsedWidth($toString = false){
		$nonRawSiblings = max(1, (int)$this->props["nonRawSiblings"]);
		$width = $this->getAttribute('width') ?: ((100 / $nonRawSiblings) . '%');

		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($width, ['parseFloatToInt' => false]);
		$parsedWidth = $ar['parsedWidth'];
		$unit = $ar['unit'];

		if($toString){
			return "{$parsedWidth}{$unit}";
		}
		return ['unit' => $unit, 'parsedWidth' => $parsedWidth];
	}

	function getWidthAsPixel(){
		$containerWidth = $this->context->containerWidth;
		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($this->getParsedWidth(true), ['parseFloatToInt' => false]);
		$unit = $ar['unit'];
		$parsedWidth = $ar['parsedWidth'];

		if($unit === '%'){
			return ((float)$containerWidth * $parsedWidth / 100) . 'px';
		}
		return $parsedWidth . 'px';
	}

	function getColumnClass(){
		$ar = $this->getParsedWidth();
		$parsedWidth = $ar['parsedWidth'];
		$unit = $ar['unit'];
		$formattedClassNb = preg_replace('/\./', '-', (string)(int)$parsedWidth);

		switch($unit){
			case '%':
				$className = "mj-column-per-{$formattedClassNb}";
				break;
			case 'px':
			default:
				$className = "mj-column-px-{$formattedClassNb}";
				break;
		}

		if(isset($this->context->globalData)){
			$this->context->globalData->addMediaQuery($className, (int)$parsedWidth, $unit);
		}

		return $className;
	}

	function getStyles(){
		return [
			'div' => [
				'font-size' => '0',
				'line-height' => '0',
				'text-align' => 'left',
				'display' => 'inline-block',
				'width' => '100%',
				'direction' => $this->getAttribute('direction'),
				'vertical-align' => $this->getAttribute('vertical-align'),
				'background-color' => $this->getAttribute('background-color'),
			],
			'tdOutlook' => [
				'vertical-align' => $this->getAttribute('vertical-align'),
				'width' => $this->getWidthAsPixel(),
			],
		];
	}

	function render(){
		$nonRawSiblings = (int)$this->props["nonRawSiblings"];
		$containerWidth = $this->context->containerWidth;
		$groupChildContext = $this->getChildContext();
		$groupWidth = $groupChildContext->containerWidth;

		$classesName = $this->getColumnClass() . ' mj-outlook-group-fix';
		if($this->getAttribute('css-class')){
			$classesName .= ' ' . $this->getAttribute('css-class');
		}

		$bgColor = $this->getAttribute('background-color');

		$tableAttrs = $this->htmlAttributes([
			'bgcolor' => ($bgColor && $bgColor !== 'none') ? $bgColor : null,
			'border' => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
			'role' => 'presentation',
		]);

		// Render each column child with mobileWidth override and Outlook td wrapping
		$group = $this;
		$renderedChildren = $this->renderChildren(function($component) use ($containerWidth, $nonRawSiblings, $groupWidth, $group){
			// Set mobileWidth so columns inside a group use their actual width on mobile
			$component->attributes['mobileWidth'] = 'mobileWidth';

			$elementWidth = $group->_getElementWidth($component, $containerWidth, $nonRawSiblings, $groupWidth);

			$tdAttrs = $component->htmlAttributes([
				'style' => [
					'align' => $component->getAttribute('align'),
					'vertical-align' => $component->getAttribute('vertical-align'),
					'width' => $elementWidth,
				],
			]);

			return "
			<!--[if mso | IE]><td {$tdAttrs}><![endif]-->
				{$component->render()}
			<!--[if mso | IE]></td><![endif]-->
			";
		});

		$divAttrs = $this->htmlAttributes([
			'class' => $classesName,
			'style' => 'div',
		]);

		return "
		<div {$divAttrs}>
			<!--[if mso | IE]><table {$tableAttrs}><tr><![endif]-->
				{$renderedChildren}
			<!--[if mso | IE]></tr></table><![endif]-->
		</div>
		";
	}

	function _getElementWidth($component, $containerWidth, $nonRawSiblings, $groupWidth){
		if(method_exists($component, 'getWidthAsPixel')){
			$width = $component->getWidthAsPixel();
		}else{
			$width = $component->getAttribute('width');
		}

		if(!$width){
			return ((int)$containerWidth / $nonRawSiblings) . 'px';
		}

		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($width, ['parseFloatToInt' => false]);
		if($ar['unit'] === '%'){
			return ($ar['parsedWidth'] * (float)$groupWidth / 100) . 'px';
		}
		return $ar['parsedWidth'] . $ar['unit'];
	}
}
