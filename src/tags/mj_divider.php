<?php
namespace Yarri\Mjml\Tags;

class MjDivider extends _Tag {

	static $componentName = 'mj-divider';

	static $allowedAttributes = [
		'border-color' => 'color',
		'border-style' => 'string',
		'border-width' => 'unit(px)',
		'container-background-color' => 'color',
		'padding' => 'unit(px,%){1,4}',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'width' => 'unit(px,%)',
		'align' => 'enum(left,center,right)',
	];

	static $defaultAttributes = [
		'border-color' => '#000000',
		'border-style' => 'solid',
		'border-width' => '4px',
		'padding' => '10px 25px',
		'width' => '100%',
		'align' => 'center',
	];

	function getStyles(){
		$align = $this->getAttribute('align');
		if($align === 'left'){
			$margin = '0px';
		} elseif($align === 'right'){
			$margin = '0px 0px 0px auto';
		} else {
			$margin = '0px auto';
		}

		$p = [
			'border-top' => $this->getAttribute('border-style') . ' ' . $this->getAttribute('border-width') . ' ' . $this->getAttribute('border-color'),
			'font-size' => '1px',
			'margin' => $margin,
			'width' => $this->getAttribute('width'),
		];

		return [
			'p' => $p,
			'outlook' => array_merge($p, [
				'width' => $this->getOutlookWidth(),
			]),
		];
	}

	function getOutlookWidth(){
		$containerWidth = $this->context->containerWidth;
		$paddingSize = $this->getShorthandAttrValue('padding', 'left')
					 + $this->getShorthandAttrValue('padding', 'right');
		$width = $this->getAttribute('width');
		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($width, ['parseFloatToInt' => false]);
		$parsedWidth = $ar['parsedWidth'];
		$unit = $ar['unit'];

		switch($unit){
			case '%':
				$effectiveWidth = (int)$containerWidth - $paddingSize;
				return ($effectiveWidth * $parsedWidth / 100) . 'px';
			case 'px':
				return $width;
			default:
				return ((int)$containerWidth - $paddingSize) . 'px';
		}
	}

	function renderAfter(){
		return '
		<!--[if mso | IE]>
		<table
			' . $this->htmlAttributes([
				'align' => $this->getAttribute('align'),
				'border' => '0',
				'cellpadding' => '0',
				'cellspacing' => '0',
				'style' => 'outlook',
				'role' => 'presentation',
				'width' => $this->getOutlookWidth(),
			]) . '
		>
			<tr>
				<td style="height:0;line-height:0;">
					&nbsp;
				</td>
			</tr>
		</table>
		<![endif]-->
		';
	}

	function render(){
		return '<p ' . $this->htmlAttributes(['style' => 'p']) . '></p>'
			. $this->renderAfter();
	}
}
