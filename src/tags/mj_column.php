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
					] : $tableStyle
				,
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
		/*
		const {
			containerWidth
		} = this.context;
		const {
			nonRawSiblings
		} = this.props;
		const width = this.getAttribute('width');
		const mobileWidth = this.getAttribute('mobileWidth');

		if (mobileWidth !== 'mobileWidth') {
			return '100%';
		}

		if (width === undefined) {
			return `${parseInt(100 / nonRawSiblings, 10)}%`;
		}

		const {
			unit,
			parsedWidth
		} = (0, _widthParser.default)(width, {
			parseFloatToInt: false
		});

		switch ($unit) {
			case '%':
				return $width;

			case 'px':
			default:
				return `${parsedWidth / parseInt(containerWidth, 10)}%`;
		}
		*/
	}

	function getWidthAsPixel(){
		// ???
	}

	function getParsedWidth($toString = false){
		$nonRawSiblings = sizeof($this->props["nonRawSiblings"]);
		$nonRawSiblings = max(1,$nonRawSiblings); // ?? toto nesmi byt 0
		$width = $this->getAttribute("width") ? $this->getAttribute("width") : (100 / $nonRawSiblings);

		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($width,["parseFloatToInt" => false]);
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
		//$addMediaQuery = $this->context->addMediaQuery; // TODO
		$className = "";
		$ar = $this->getParsedWidth();
		$parsedWidth = $ar["parsedWidth"];
		$unit = $ar["unit"];
		$formattedClassNb = preg_replace('/\./','-',$parsedWidth);

		
		switch($unit){
			case '%':
				$className = "mj-column-per-{$formattedClassNb}";
				break;

			case 'px':
			default:
				$className = "mj-column-px-{$formattedClassNb}";
				break;
		} // Add className to media queries

		/* TODO
		$addMediaQuery($className, [
			$parsedWidth,
			$unit
		]);
		*/
		return $className;
	}

	function hasGutter(){
		foreach(['padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top'] as $attr){
			if(!is_null($this->getAttribute($attr))){ return true; }
		};
		return false;
	}

	function render(){
		$classesName = "{$this->getColumnClass()} mj-outlook-group-fix";

		if ($this->getAttribute('css-class')) {
			$classesName += " {$this->getAttribute('css-class')}";
		}

		return "
		<div
			{$this->htmlAttributes([
			"class" => "classesName",
			"style" => "div"
			])}
		>
			".$this->hasGutter() ? $this->renderGutter() : $this->renderColumn()."
		</div>
		";
	}

	function renderGutter(){
		// ???
	}
}
