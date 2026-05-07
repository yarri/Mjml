<?php
namespace Yarri\Mjml\Tags;

class MjAccordionTitle extends _Tag {

	static $componentName = 'mj-accordion-title';

	static $endingTag = true;

	static $allowedAttributes = [
		'background-color' => 'color',
		'color' => 'color',
		'font-size' => 'unit(px)',
		'font-family' => 'string',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'padding' => 'unit(px,%){1,4}',
	];

	static $defaultAttributes = [
		'font-size' => '13px',
		'padding' => '16px',
	];

	function resolveFontFamily(){
		$own = $this->getAttribute('font-family');
		if(!is_null($own) && $own !== '') return $own;
		if(isset($this->context->elementFontFamily) && (string)$this->context->elementFontFamily !== '') return $this->context->elementFontFamily;
		if(isset($this->context->accordionFontFamily) && (string)$this->context->accordionFontFamily !== '') return $this->context->accordionFontFamily;
		return null;
	}

	function getStyles(){
		return [
			'td' => [
				'width' => '100%',
				'background-color' => $this->getAttribute('background-color'),
				'color' => $this->getAttribute('color'),
				'font-size' => $this->getAttribute('font-size'),
				'font-family' => $this->resolveFontFamily(),
				'padding-bottom' => $this->getAttribute('padding-bottom'),
				'padding-left' => $this->getAttribute('padding-left'),
				'padding-right' => $this->getAttribute('padding-right'),
				'padding-top' => $this->getAttribute('padding-top'),
				'padding' => $this->getAttribute('padding'),
			],
			'table' => [
				'width' => '100%',
				'border-bottom' => $this->getAttribute('border'),
			],
			'td2' => [
				'padding' => '16px',
				'background' => $this->getAttribute('background-color'),
				'vertical-align' => $this->getAttribute('icon-align'),
			],
			'img' => [
				'display' => 'none',
				'width' => $this->getAttribute('icon-width'),
				'height' => $this->getAttribute('icon-height'),
			],
		];
	}

	function renderTitle(){
		return "
		<td {$this->htmlAttributes(['class' => $this->getAttribute('css-class'), 'style' => 'td'])}>
			{$this->getContent()}
		</td>
		";
	}

	function renderIcons(){
		$td2Attrs = $this->htmlAttributes([
			'class' => 'mj-accordion-ico',
			'style' => 'td2',
		]);
		$imgWrappedAttrs = $this->htmlAttributes([
			'src' => $this->getAttribute('icon-wrapped-url'),
			'alt' => $this->getAttribute('icon-wrapped-alt'),
			'class' => 'mj-accordion-more',
			'style' => 'img',
		]);
		$imgUnwrappedAttrs = $this->htmlAttributes([
			'src' => $this->getAttribute('icon-unwrapped-url'),
			'alt' => $this->getAttribute('icon-unwrapped-alt'),
			'class' => 'mj-accordion-less',
			'style' => 'img',
		]);

		return "
		<!--[if !mso | IE]><!-->
		<td {$td2Attrs}>
			<img {$imgWrappedAttrs} />
			<img {$imgUnwrappedAttrs} />
		</td>
		<!--<![endif]-->
		";
	}

	function render(){
		$tableAttrs = $this->htmlAttributes([
			'cellspacing' => '0',
			'cellpadding' => '0',
			'style' => 'table',
		]);

		$titleEl = $this->renderTitle();
		$iconsEl = $this->renderIcons();

		$content = $this->getAttribute('icon-position') === 'right'
			? $titleEl . "\n" . $iconsEl
			: $iconsEl . "\n" . $titleEl;

		return "
		<div {$this->htmlAttributes(['class' => 'mj-accordion-title'])}>
			<table {$tableAttrs}>
				<tbody>
					<tr>
						{$content}
					</tr>
				</tbody>
			</table>
		</div>
		";
	}
}
