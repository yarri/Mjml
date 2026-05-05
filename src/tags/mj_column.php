<?php
namespace Yarri\Mjml\Tags;

class MjColumn extends _Tag {

	static $componentName = "mj-column";

	static $allowedAttributes = [
		'background-color' => 'color',
		'border' => 'string',
		'border-bottom' => 'string',
		'border-left' => 'string',
		'border-radius' => 'unit(px,%){1,4}',
		'border-right' => 'string',
		'border-top' => 'string',
		'direction' => 'enum(ltr,rtl)',
		'inner-background-color' => 'color',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'inner-border' => 'string',
		'inner-border-bottom' => 'string',
		'inner-border-left' => 'string',
		'inner-border-radius' => 'unit(px,%){1,4}',
		'inner-border-right' => 'string',
		'inner-border-top' => 'string',
		'padding' => 'unit(px,%){1,4}',
		'vertical-align' => 'enum(top,bottom,middle)',
		'width' => 'unit(px,%)'
	];

	static $defaultAttributes = [
		'direction' => 'ltr',
		'vertical-align' => 'top'
	];

	function getChildContext(){
		$containerWidth = $this->context->containerWidth;
		$nonRawSiblings = max(1, (int)$this->props["nonRawSiblings"]);

		$ar = $this->getBoxWidths();
		$borders = $ar['borders'];
		$paddings = $ar['paddings'];

		$innerBorders = $this->getShorthandAttrValue('inner-border', 'left')
					  + $this->getShorthandAttrValue('inner-border', 'right');

		$allPaddings = $paddings + $borders + $innerBorders;

		$columnWidth = $this->getAttribute('width')
					?: ((float)$containerWidth / $nonRawSiblings) . 'px';

		$ar2 = \Yarri\Mjml\Core\Lib\Helpers::widthParser($columnWidth, ['parseFloatToInt' => false]);
		$unit = $ar2['unit'];
		$parsedWidth = $ar2['parsedWidth'];

		if($unit === '%'){
			$inner = ((float)$containerWidth * $parsedWidth / 100) - $allPaddings;
		}else{
			$inner = $parsedWidth - $allPaddings;
		}

		$context = clone $this->context;
		$context->containerWidth = "{$inner}px";
		return $context;
	}

	function getStyles(){
		$tableStyle = [
			'background-color' => $this->getAttribute('background-color'),
			'border' => $this->getAttribute('border'),
			'border-bottom' => $this->getAttribute('border-bottom'),
			'border-left' => $this->getAttribute('border-left'),
			'border-radius' => $this->getAttribute('border-radius'),
			'border-right' => $this->getAttribute('border-right'),
			'border-top' => $this->getAttribute('border-top'),
			'vertical-align' => $this->getAttribute('vertical-align')
		];

		return [
			'div' => [
				'font-size' => '0px',
				'text-align' => 'left',
				'direction' => $this->getAttribute('direction'),
				'display' => 'inline-block',
				'vertical-align' => $this->getAttribute('vertical-align'),
				'width' => $this->getMobileWidth()
			],
			'table' => $this->hasGutter() ? [
				'background-color' => $this->getAttribute('inner-background-color'),
				'border' => $this->getAttribute('inner-border'),
				'border-bottom' => $this->getAttribute('inner-border-bottom'),
				'border-left' => $this->getAttribute('inner-border-left'),
				'border-radius' => $this->getAttribute('inner-border-radius'),
				'border-right' => $this->getAttribute('inner-border-right'),
				'border-top' => $this->getAttribute('inner-border-top')
			] : $tableStyle,
			'tdOutlook' => [
				'vertical-align' => $this->getAttribute('vertical-align'),
				'width' => $this->getWidthAsPixel()
			],
			'gutter' => $tableStyle + [
				'padding' => $this->getAttribute('padding'),
				'padding-top' => $this->getAttribute('padding-top'),
				'padding-right' => $this->getAttribute('padding-right'),
				'padding-bottom' => $this->getAttribute('padding-bottom'),
				'padding-left' => $this->getAttribute('padding-left')
			]
		];
	}

	function getMobileWidth(){
		$containerWidth = $this->context->containerWidth;
		$nonRawSiblings = max(1, (int)$this->props["nonRawSiblings"]);
		$width = $this->getAttribute('width');
		$mobileWidth = $this->getAttribute('mobileWidth');

		if($mobileWidth !== 'mobileWidth'){
			return '100%';
		}

		if(is_null($width)){
			return ((int)(100 / $nonRawSiblings)) . '%';
		}

		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($width, ['parseFloatToInt' => false]);
		$unit = $ar['unit'];
		$parsedWidth = $ar['parsedWidth'];

		switch($unit){
			case '%':
				return $width;
			case 'px':
			default:
				return ($parsedWidth / (int)$containerWidth) . '%';
		}
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

	function getParsedWidth($toString = false){
		$nonRawSiblings = max(1, (int)$this->props["nonRawSiblings"]);
		$width = $this->getAttribute("width") ?: ((100 / $nonRawSiblings) . "%");

		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($width, ["parseFloatToInt" => false]);
		$parsedWidth = $ar["parsedWidth"];
		$unit = $ar["unit"];

		if($toString){
			return "{$parsedWidth}{$unit}";
		}

		return [
			"unit" => $unit,
			"parsedWidth" => $parsedWidth
		];
	}

	function getColumnClass(){
		$ar = $this->getParsedWidth();
		$parsedWidth = $ar["parsedWidth"];
		$unit = $ar["unit"];
		$formattedClassNb = preg_replace('/\./', '-', (string)$parsedWidth);

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
			$this->context->globalData->addMediaQuery($className, $parsedWidth, $unit);
		}

		return $className;
	}

	function hasGutter(){
		foreach(['padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top'] as $attr){
			if(!is_null($this->getAttribute($attr))){ return true; }
		}
		return false;
	}

	function renderGutter(){
		$tdAttrs = $this->htmlAttributes(['style' => 'gutter']);
		$columnContent = $this->renderColumn();

		return "
		<table
			{$this->htmlAttributes([
				'border' => '0',
				'cellpadding' => '0',
				'cellspacing' => '0',
				'role' => 'presentation',
				'width' => '100%'
			])}
		>
			<tbody>
				<tr>
					<td {$tdAttrs}>
						{$columnContent}
					</td>
				</tr>
			</tbody>
		</table>
		";
	}

	function renderColumn(){
		$tableAttrs = $this->htmlAttributes([
			'border' => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
			'role' => 'presentation',
			'style' => 'table',
			'width' => '100%'
		]);

		$renderedChildren = $this->renderChildren(function($component){
			$tdAttrs = $component->htmlAttributes([
				'align' => $component->getAttribute('align'),
				'vertical-align' => $component->getAttribute('vertical-align'),
				'class' => $component->getAttribute('css-class'),
				'style' => [
					'background' => $component->getAttribute('container-background-color'),
					'font-size' => '0px',
					'padding' => $component->getAttribute('padding'),
					'padding-top' => $component->getAttribute('padding-top'),
					'padding-right' => $component->getAttribute('padding-right'),
					'padding-bottom' => $component->getAttribute('padding-bottom'),
					'padding-left' => $component->getAttribute('padding-left'),
					'word-break' => 'break-word'
				]
			]);
			$rendered = $component->render();
			return "
			<tr>
				<td {$tdAttrs}>
					{$rendered}
				</td>
			</tr>
			";
		});

		return "
		<table {$tableAttrs}>
			<tbody>
				{$renderedChildren}
			</tbody>
		</table>
		";
	}

	function render(){
		$classesName = "{$this->getColumnClass()} mj-outlook-group-fix";

		if($this->getAttribute('css-class')){
			$classesName .= " {$this->getAttribute('css-class')}";
		}

		$divAttrs = $this->htmlAttributes([
			'class' => $classesName,
			'style' => 'div'
		]);
		$inner = $this->hasGutter() ? $this->renderGutter() : $this->renderColumn();

		return "
		<div {$divAttrs}>
			{$inner}
		</div>
		";
	}
}
