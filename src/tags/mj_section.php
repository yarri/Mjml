<?php
namespace Yarri\Mjml\Tags;

class MjSection extends _Tag {

	var $allowedAttributes = [
		'background-color' => 'color',
		'background-url' => 'string',
		'background-repeat' => 'enum(repeat,no-repeat)',
		'background-size' => 'string',
		'background-position' => 'string',
		'background-position-x' => 'string',
		'background-position-y' => 'string',
		'border' => 'string',
		'border-bottom' => 'string',
		'border-left' => 'string',
		'border-radius' => 'string',
		'border-right' => 'string',
		'border-top' => 'string',
		'direction' => 'enum(ltr,rtl)',
		'full-width' => 'enum(full-width,false,)',
		'padding' => 'unit(px,%){1,4}',
		'padding-top' => 'unit(px,%)',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'text-align' => 'enum(left,center,right)',
		'text-padding' => 'unit(px,%){1,4}'
	];

	var $defaultAttributes = [
		'background-repeat' => 'repeat',
		'background-size' => 'auto',
		'background-position' => 'top center',
		'direction' => 'ltr',
		'padding' => '20px 0',
		'text-align' => 'center',
		'text-padding' => '4px 4px 4px 0'
	];

	function getStyles(){
		/*
		const {
			containerWidth
		} = this.context;
		*/
		$containerWidth = $this->context->containerWidth;
		$fullWidth = $this->isFullWidth();
		$background = $this->getAttribute('background-url') ? [
			'background' => $this->getBackground(),
			// background size, repeat and position has to be seperate since yahoo does not support shorthand background css property
			'background-position' => $this->getBackgroundString(),
			'background-repeat' => $this->getAttribute('background-repeat'),
			'background-size' => $this->getAttribute('background-size')
		] : [
			'background' => $this->getAttribute('background-color'),
			'background-color' => $this->getAttribute('background-color')
		];
		return [
			'tableFullwidth' => ($fullWidth ? $background : []) + [
					'width' => '100%',
					'border-radius' => $this->getAttribute('border-radius')
				],
			'table' => ($fullWidth ? [] : $background) + [
				'width' => '100%',
				'border-radius' => $this->getAttribute('border-radius')
			],
			'td' => [
				'border' => $this->getAttribute('border'),
				'border-bottom' => $this->getAttribute('border-bottom'),
				'border-left' => $this->getAttribute('border-left'),
				'border-right' => $this->getAttribute('border-right'),
				'border-top' => $this->getAttribute('border-top'),
				'direction' => $this->getAttribute('direction'),
				'font-size' => '0px',
				'padding' => $this->getAttribute('padding'),
				'padding-bottom' => $this->getAttribute('padding-bottom'),
				'padding-left' => $this->getAttribute('padding-left'),
				'padding-right' => $this->getAttribute('padding-right'),
				'padding-top' => $this->getAttribute('padding-top'),
				'text-align' => $this->getAttribute('text-align')
			],
			'div' => ($fullWidth ? [] : $background) + [
				'margin' => '0px auto',
				'border-radius' => $this->getAttribute('border-radius'),
				'max-width' => $containerWidth
			],
			'innerDiv' => [
				'line-height' => '0',
				'font-size' => '0'
			]
		];
	}

	function getBackground(){
		$ar = [];
		$ar[] = $this->getAttribute('background-color');
		if($this->hasBackground()){
			$ar[] = "url('{$this->getAttribute('background-url')}')";
			$ar[] = $this->getBackgroundString();
			$ar[] = "/ {$this->getAttribute('background-size')}";
			$ar[] = $this->getAttribute('background-repeat');
		}
		return $this->makeBackgroundString($out);
	}

	function getBackgroundString(){
		$ar = $this->getBackgroundPosition();
		$posX = $ar["posX"];
		$posY = $ar["posY"];
		return "$posX $posY";
	}

	function getBackgroundPosition(){
    $ar = $this->parseBackgroundPosition();
		$x = $ar["x"];
		$y = $ar["y"];
    return [
      "posX" => $this->getAttribute('background-position-x') ? $this->getAttribute('background-position-x') : $x,
      "posY" => $this->getAttribute('background-position-y') ? $this->getAttribute('background-position-y') : $y
    ];
	}

	function parseBackgroundPosition(){
			$posSplit = explode(' ',$this->getAttribute('background-position'));

			if (sizeof($posSplit) === 1) {
				$val = $posSplit[0]; // here we must determine if x or y was provided ; other will be center

				if (in_array($val,['top', 'bottom'])) {
					return [
						'x' => 'center',
						'y' => $val
					];
				}

				return [
					'x' => $val,
					'y' => 'center'
				];
			}

			if (sizeof($posSplit) === 2) {
				// x and y can be put in any order in background-position so we need to determine that based on values
				$val1 = $posSplit[0];
				$val2 = $posSplit[1];

				if (in_array($val1,['top', 'bottom']) || $val1 === 'center' && in_array($val2,['left', 'right'])) {
					return [
						'x' => $val2,
						'y' => $val1
					];
				}

				return [
					'x' => $val1,
					'y' => $val2
				];
			} // more than 2 values is not supported, let's treat as default value


			return [
				'x' => 'center',
				'y' => 'top'
			];
	}

	function hasBackground(){
		return strlen((string)$this->getAttribute('background-url'))>0;
	}

	function isFullWidth(){
		return $this->getAttribute('full-width') === 'full-width';
	}

	function renderBefore(){
		/*
		const {
			containerWidth
		} = this.context;
		*/
		$containerWidth = $this->context->containerWidth;
		$bgcolorAttr = $this->getAttribute('background-color') ? [
			"bgcolor" => $this->getAttribute('background-color')
		] : [];
		return "
		<!--[if mso | IE]>
		<table
			{$this->htmlAttributes([
				'align' => 'center',
				'border' => '0',
				'cellpadding' => '0',
				'cellspacing' => '0',
				'class' => $this->suffixCssClasses($this->getAttribute('css-class'), 'outlook'),
				'role' => 'presentation',
				'style' => [
					'width' => $containerWidth
				],
				'width' => (int)$containerWidth,
			] + $bgcolorAttr)}
		>
			<tr>
				<td style=\"line-height:0px;font-size:0px;mso-line-height-rule:exactly;\">
		<![endif]-->
		";
	}

	function renderAfter(){
		return "
		<!--[if mso | IE]>
				</td>
			</tr>
		</table>
		<![endif]-->
		";
	}

	function renderWrappedChildren(){

	}

	function renderWithBackground($content){

	}

	function renderSection(){

	}

	function renderFullWidth(){
		$content = $this->hasBackground() ? $this->renderWithBackground("
			{$this->renderBefore()}
			{$this->renderSection()}
			{$this->renderAfter()}
		") : "
			{$this->renderBefore()}
			{$this->renderSection()}
			{$this->renderAfter()}
		";
		return "
			<table
				{$this->htmlAttributes([
				'align' => 'center',
				'class' => $this->getAttribute('css-class'),
				'background' => $this->getAttribute('background-url'),
				'border' => '0',
				'cellpadding' => '0',
				'cellspacing' => '0',
				'role' => 'presentation',
				'style' => 'tableFullwidth'
			])}
			>
				<tbody>
					<tr>
						<td>
							{$content}
						</td>
					</tr>
				</tbody>
			</table>
		";
	}

	function renderSimple(){
		$section = $this->renderSection();
		return
			$this->renderBefore().
			($this->hasBackground() ? $this->renderWithBackground($section) : $section).
			$this->renderAfter();
	}

	function render(){
		return $this->isFullWidth() ? $this->renderFullWidth() : $this->renderSimple();
	}
	
}
