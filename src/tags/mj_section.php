<?php
namespace Yarri\Mjml\Tags;

class MjSection extends _Tag {

	static $componentName = "mj-section";

	static $allowedAttributes = [
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

	static $defaultAttributes = [
		'background-repeat' => 'repeat',
		'background-size' => 'auto',
		'background-position' => 'top center',
		'direction' => 'ltr',
		'padding' => '20px 0',
		'text-align' => 'center',
		'text-padding' => '4px 4px 4px 0'
	];

	function hasBorderRadius(){
		$borderRadius = $this->getAttribute('border-radius');
		return $borderRadius !== '' && !is_null($borderRadius);
	}

	function getChildContext(){
		$ar = $this->getBoxWidths();
		$box = $ar['box'];
		$context = clone $this->context;
		$context->containerWidth = "{$box}px";
		return $context;
	}

	function getStyles(){
		$containerWidth = $this->context->containerWidth;
		$fullWidth = $this->isFullWidth();
		$hasBorderRadius = $this->hasBorderRadius();

		$background = $this->getAttribute('background-url') ? [
			'background' => $this->getBackground(),
			'background-position' => $this->getBackgroundString(),
			'background-repeat' => $this->getAttribute('background-repeat'),
			'background-size' => $this->getAttribute('background-size')
		] : [
			'background' => $this->getAttribute('background-color'),
			'background-color' => $this->getAttribute('background-color')
		];

		$tableStyle = ($fullWidth ? [] : $background) + ['width' => '100%'];
		if($hasBorderRadius){
			$tableStyle['border-collapse'] = 'separate';
		}

		$divStyle = ($fullWidth ? [] : $background) + [
			'margin' => '0px auto',
			'max-width' => $containerWidth,
			'border-radius' => $this->getAttribute('border-radius')
		];
		if($hasBorderRadius){
			$divStyle['overflow'] = 'hidden';
		}

		return [
			'tableFullwidth' => ($fullWidth ? $background : []) + [
				'width' => '100%'
			],
			'table' => $tableStyle,
			'td' => [
				'border' => $this->getAttribute('border'),
				'border-bottom' => $this->getAttribute('border-bottom'),
				'border-left' => $this->getAttribute('border-left'),
				'border-right' => $this->getAttribute('border-right'),
				'border-top' => $this->getAttribute('border-top'),
				'border-radius' => $this->getAttribute('border-radius'),
				'direction' => $this->getAttribute('direction'),
				'font-size' => '0px',
				'padding' => $this->getAttribute('padding'),
				'padding-bottom' => $this->getAttribute('padding-bottom'),
				'padding-left' => $this->getAttribute('padding-left'),
				'padding-right' => $this->getAttribute('padding-right'),
				'padding-top' => $this->getAttribute('padding-top'),
				'text-align' => $this->getAttribute('text-align')
			],
			'div' => $divStyle,
			'innerDiv' => [
				'line-height' => '0',
				'font-size' => '0'
			]
		];
	}

	function getBackground(){
		$parts = [];
		$parts[] = $this->getAttribute('background-color');
		if($this->hasBackground()){
			$parts[] = "url('{$this->getAttribute('background-url')}')";
			$parts[] = $this->getBackgroundString();
			$parts[] = "/ {$this->getAttribute('background-size')}";
			$parts[] = $this->getAttribute('background-repeat');
		}
		return join(' ', array_filter($parts, function($v){
			return !is_null($v) && strlen((string)$v) > 0;
		}));
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
			"posX" => $this->getAttribute('background-position-x') ?: $x,
			"posY" => $this->getAttribute('background-position-y') ?: $y
		];
	}

	function parseBackgroundPosition(){
		$posSplit = explode(' ', $this->getAttribute('background-position'));

		if(sizeof($posSplit) === 1){
			$val = $posSplit[0];
			if(in_array($val, ['top', 'bottom'])){
				return ['x' => 'center', 'y' => $val];
			}
			return ['x' => $val, 'y' => 'center'];
		}

		if(sizeof($posSplit) === 2){
			$val1 = $posSplit[0];
			$val2 = $posSplit[1];
			if(in_array($val1, ['top', 'bottom']) || ($val1 === 'center' && in_array($val2, ['left', 'right']))){
				return ['x' => $val2, 'y' => $val1];
			}
			return ['x' => $val1, 'y' => $val2];
		}

		return ['x' => 'center', 'y' => 'top'];
	}

	function hasBackground(){
		return strlen((string)$this->getAttribute('background-url')) > 0;
	}

	function isFullWidth(){
		return $this->getAttribute('full-width') === 'full-width';
	}

	function renderBefore(){
		$containerWidth = $this->context->containerWidth;
		$bgcolorAttr = $this->getAttribute('background-color') ? [
			"bgcolor" => $this->getAttribute('background-color')
		] : [];

		return "
		<!--[if mso | IE]>
		<table
			{$this->htmlAttributes(array_merge([
				'align' => 'center',
				'border' => '0',
				'cellpadding' => '0',
				'cellspacing' => '0',
				'class' => $this->suffixCssClasses($this->getAttribute('css-class'), 'outlook'),
				'role' => 'presentation',
				'style' => ['width' => $containerWidth],
				'width' => (int)$containerWidth,
			], $bgcolorAttr))}
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
		$section = $this;
		$wrappedChildren = $this->renderChildren(function($component) use ($section){
			return "
			<!--[if mso | IE]><td {$component->htmlAttributes([
				'align' => $component->getAttribute('align'),
				'class' => $section->suffixCssClasses($component->getAttribute('css-class'), 'outlook'),
				'style' => 'tdOutlook'
			])}><![endif]-->
				{$component->render()}
			<!--[if mso | IE]></td><![endif]-->
			";
		});

		return "
		<!--[if mso | IE]><tr><![endif]-->
			$wrappedChildren
		<!--[if mso | IE]></tr><![endif]-->
		";
	}

	function renderWithBackground($content){
		$fullWidth = $this->isFullWidth();
		$containerWidth = $this->context->containerWidth;

		$isPercentage = function($str){
			return (bool)preg_match('/^\d+(\.\d+)?%$/', (string)$str);
		};

		$ar = $this->getBackgroundPosition();
		$bgPosX = $ar['posX'];
		$bgPosY = $ar['posY'];

		switch($bgPosX){
			case 'left':   $bgPosX = '0%'; break;
			case 'center': $bgPosX = '50%'; break;
			case 'right':  $bgPosX = '100%'; break;
			default:
				if(!$isPercentage($bgPosX)){ $bgPosX = '50%'; }
				break;
		}
		switch($bgPosY){
			case 'top':    $bgPosY = '0%'; break;
			case 'center': $bgPosY = '50%'; break;
			case 'bottom': $bgPosY = '100%'; break;
			default:
				if(!$isPercentage($bgPosY)){ $bgPosY = '0%'; }
				break;
		}

		$bgRepeat = $this->getAttribute('background-repeat') === 'repeat';

		$calcPos = function($pos, $isX) use ($isPercentage, $bgRepeat){
			if($isPercentage($pos)){
				$pct = (float)preg_replace('/%$/', '', $pos);
				$decimal = $pct / 100;
				if($bgRepeat){
					return [$decimal, $decimal];
				}else{
					$v = (-50 + $decimal * 100) / 100;
					return [$v, $v];
				}
			}elseif($bgRepeat){
				$origin = $isX ? '0.5' : '0';
				$p = $isX ? '0.5' : '0';
				return [$origin, $p];
			}else{
				$origin = $isX ? '0' : '-0.5';
				$p = $isX ? '0' : '-0.5';
				return [$origin, $p];
			}
		};

		list($vOriginX, $vPosX) = $calcPos($bgPosX, true);
		list($vOriginY, $vPosY) = $calcPos($bgPosY, false);

		$bgSize = $this->getAttribute('background-size');
		$vSizeAttributes = [];

		if($bgSize === 'cover' || $bgSize === 'contain'){
			$vSizeAttributes = [
				'size' => '1,1',
				'aspect' => $bgSize === 'cover' ? 'atleast' : 'atmost',
			];
		}elseif($bgSize !== 'auto'){
			$bgSplit = explode(' ', $bgSize);
			if(count($bgSplit) === 1){
				$vSizeAttributes = ['size' => $bgSize, 'aspect' => 'atmost'];
			}else{
				$vSizeAttributes = ['size' => join(',', $bgSplit)];
			}
		}

		$vmlType = $this->getAttribute('background-repeat') === 'no-repeat' ? 'frame' : 'tile';

		if($bgSize === 'auto'){
			$vmlType = 'tile';
			$vOriginX = 0.5; $vPosX = 0.5;
			$vOriginY = 0;   $vPosY = 0;
		}

		$rectStyleAttr = $fullWidth ? ['mso-width-percent' => '1000'] : ['width' => $containerWidth];

		$vFillAttributes = array_merge([
			'origin'   => "{$vOriginX}, {$vOriginY}",
			'position' => "{$vPosX}, {$vPosY}",
			'src'      => $this->getAttribute('background-url'),
			'color'    => $this->getAttribute('background-color'),
			'type'     => $vmlType,
		], $vSizeAttributes);

		$rectAttrs = $this->htmlAttributes([
			'style'   => $rectStyleAttr,
			'xmlns:v' => 'urn:schemas-microsoft-com:vml',
			'fill'    => 'true',
			'stroke'  => 'false',
		]);
		$fillAttrs = $this->htmlAttributes($vFillAttributes);

		return "
		<!--[if mso | IE]>
			<v:rect {$rectAttrs}>
			<v:fill {$fillAttrs} />
			<v:textbox style=\"mso-fit-shape-to-text:true\" inset=\"0,0,0,0\">
		<![endif]-->
			{$content}
		<!--[if mso | IE]>
			</v:textbox>
			</v:rect>
		<![endif]-->
		";
	}

	function renderSection(){
		$hasBackground = $this->hasBackground();

		$divAttrs = $this->htmlAttributes([
			'class' => $this->isFullWidth() ? null : $this->getAttribute('css-class'),
			'style' => 'div'
		]);
		$tableAttrs = $this->htmlAttributes([
			'align'       => 'center',
			'background'  => $this->isFullWidth() ? null : $this->getAttribute('background-url'),
			'border'      => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
			'role'        => 'presentation',
			'style'       => 'table'
		]);
		$tdAttrs = $this->htmlAttributes(['style' => 'td']);
		$innerDivAttrs = $this->htmlAttributes(['style' => 'innerDiv']);
		$wrappedChildren = $this->renderWrappedChildren();

		$out = [];
		$out[] = "<div {$divAttrs}>";
		if($hasBackground){ $out[] = "<div {$innerDivAttrs}>"; }
		$out[] = "<table {$tableAttrs}>";
		$out[] = "<tbody><tr><td {$tdAttrs}>";
		$out[] = "<!--[if mso | IE]><table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><![endif]-->";
		$out[] = $wrappedChildren;
		$out[] = "<!--[if mso | IE]></table><![endif]-->";
		$out[] = "</td></tr></tbody></table>";
		if($hasBackground){ $out[] = "</div>"; }
		$out[] = "</div>";

		return join("\n", $out);
	}

	function renderFullWidth(){
		$content = $this->hasBackground()
			? $this->renderWithBackground(
				$this->renderBefore() .
				$this->renderSection() .
				$this->renderAfter()
			)
			: $this->renderBefore() . $this->renderSection() . $this->renderAfter();

		$tableAttrs = $this->htmlAttributes([
			'align'       => 'center',
			'class'       => $this->getAttribute('css-class'),
			'background'  => $this->getAttribute('background-url'),
			'border'      => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
			'role'        => 'presentation',
			'style'       => 'tableFullwidth'
		]);

		return "
		<table {$tableAttrs}>
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
		return $this->renderBefore()
			. ($this->hasBackground() ? $this->renderWithBackground($section) : $section)
			. $this->renderAfter();
	}

	function render(){
		return $this->isFullWidth() ? $this->renderFullWidth() : $this->renderSimple();
	}
}
