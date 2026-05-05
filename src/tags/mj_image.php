<?php
namespace Yarri\Mjml\Tags;

class MjImage extends _Tag {

	static $componentName = 'mj-image';

	static $allowedAttributes = [
		'alt' => 'string',
		'href' => 'string',
		'name' => 'string',
		'src' => 'string',
		'srcset' => 'string',
		'sizes' => 'string',
		'title' => 'string',
		'rel' => 'string',
		'align' => 'enum(left,center,right)',
		'border' => 'string',
		'border-bottom' => 'string',
		'border-left' => 'string',
		'border-right' => 'string',
		'border-top' => 'string',
		'border-radius' => 'unit(px,%){1,4}',
		'container-background-color' => 'color',
		'fluid-on-mobile' => 'boolean',
		'padding' => 'unit(px,%){1,4}',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'target' => 'string',
		'width' => 'unit(px)',
		'height' => 'unit(px,auto)',
		'max-height' => 'unit(px,%)',
		'font-size' => 'unit(px)',
		'usemap' => 'string',
	];

	static $defaultAttributes = [
		'align' => 'center',
		'border' => '0',
		'height' => 'auto',
		'padding' => '10px 25px',
		'target' => '_blank',
		'font-size' => '13px',
	];

	function getStyles(){
		$contentWidth = $this->getContentWidth();
		$fullWidth = $this->getAttribute('full-width') === 'full-width';
		$ar = \Yarri\Mjml\Core\Lib\Helpers::widthParser($contentWidth . 'px', ['parseFloatToInt' => false]);
		$parsedWidth = $ar['parsedWidth'];
		$unit = $ar['unit'];

		return [
			'img' => [
				'border' => $this->getAttribute('border'),
				'border-left' => $this->getAttribute('border-left'),
				'border-right' => $this->getAttribute('border-right'),
				'border-top' => $this->getAttribute('border-top'),
				'border-bottom' => $this->getAttribute('border-bottom'),
				'border-radius' => $this->getAttribute('border-radius'),
				'display' => 'block',
				'outline' => 'none',
				'text-decoration' => 'none',
				'height' => $this->getAttribute('height'),
				'max-height' => $this->getAttribute('max-height'),
				'min-width' => $fullWidth ? '100%' : null,
				'width' => '100%',
				'max-width' => $fullWidth ? '100%' : null,
				'font-size' => $this->getAttribute('font-size'),
			],
			'td' => [
				'width' => $fullWidth ? null : "{$parsedWidth}{$unit}",
			],
			'table' => [
				'min-width' => $fullWidth ? '100%' : null,
				'max-width' => $fullWidth ? '100%' : null,
				'width' => $fullWidth ? "{$parsedWidth}{$unit}" : null,
				'border-collapse' => 'collapse',
				'border-spacing' => '0px',
			],
		];
	}

	function getContentWidth(){
		$width = $this->getAttribute('width');
		$maxWidth = $width ? (int)$width : PHP_INT_MAX;
		$ar = $this->getBoxWidths();
		return min($ar['box'], $maxWidth);
	}

	function renderImage(){
		$height = $this->getAttribute('height');
		$heightAttr = $height ? ($height === 'auto' ? $height : (int)$height) : null;

		$imgAttrs = $this->htmlAttributes([
			'alt' => $this->getAttribute('alt'),
			'height' => $heightAttr,
			'src' => $this->getAttribute('src'),
			'srcset' => $this->getAttribute('srcset'),
			'sizes' => $this->getAttribute('sizes'),
			'style' => 'img',
			'title' => $this->getAttribute('title'),
			'width' => $this->getContentWidth(),
			'usemap' => $this->getAttribute('usemap'),
		]);

		$img = "<img {$imgAttrs} />";

		if($this->getAttribute('href')){
			$aAttrs = $this->htmlAttributes([
				'href' => $this->getAttribute('href'),
				'target' => $this->getAttribute('target'),
				'rel' => $this->getAttribute('rel'),
				'name' => $this->getAttribute('name'),
				'title' => $this->getAttribute('title'),
			]);
			return "<a {$aAttrs}>{$img}</a>";
		}

		return $img;
	}

	function render(){
		$fluidOnMobile = $this->getAttribute('fluid-on-mobile') ? 'mj-full-width-mobile' : null;

		$tableAttrs = $this->htmlAttributes([
			'border' => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
			'role' => 'presentation',
			'style' => 'table',
			'class' => $fluidOnMobile,
		]);
		$tdAttrs = $this->htmlAttributes([
			'style' => 'td',
			'class' => $fluidOnMobile,
		]);

		return "
		<table {$tableAttrs}>
			<tbody>
				<tr>
					<td {$tdAttrs}>
						{$this->renderImage()}
					</td>
				</tr>
			</tbody>
		</table>
		";
	}
}
