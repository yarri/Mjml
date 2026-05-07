<?php
namespace Yarri\Mjml\Tags;

class MjHero extends _Tag {

	static $componentName = 'mj-hero';

	static $allowedAttributes = [
		'mode' => 'string',
		'height' => 'unit(px,%)',
		'background-url' => 'string',
		'background-width' => 'unit(px,%)',
		'background-height' => 'unit(px,%)',
		'background-position' => 'string',
		'border-radius' => 'string',
		'container-background-color' => 'color',
		'inner-background-color' => 'color',
		'inner-padding' => 'unit(px,%){1,4}',
		'inner-padding-top' => 'unit(px,%)',
		'inner-padding-left' => 'unit(px,%)',
		'inner-padding-right' => 'unit(px,%)',
		'inner-padding-bottom' => 'unit(px,%)',
		'padding' => 'unit(px,%){1,4}',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'background-color' => 'color',
		'vertical-align' => 'enum(top,bottom,middle)',
	];

	static $defaultAttributes = [
		'mode' => 'fixed-height',
		'height' => '0px',
		'background-position' => 'center center',
		'padding' => '0px',
		'background-color' => '#ffffff',
		'vertical-align' => 'top',
	];

	function getChildContext(){
		$containerWidth = $this->context->containerWidth;
		$paddingSize = $this->getShorthandAttrValue('padding', 'left')
					 + $this->getShorthandAttrValue('padding', 'right');
		$context = clone $this->context;
		$context->containerWidth = ((float)$containerWidth - $paddingSize) . 'px';
		return $context;
	}

	function getBackground(){
		$parts = [];
		$bgColor = $this->getAttribute('background-color');
		if($bgColor){ $parts[] = $bgColor; }
		$bgUrl = $this->getAttribute('background-url');
		if($bgUrl){
			$bgPos = $this->getAttribute('background-position');
			$parts[] = "url('{$bgUrl}')";
			$parts[] = 'no-repeat';
			$parts[] = "{$bgPos} / cover";
		}
		return join(' ', array_filter($parts, function($v){ return (string)$v !== ''; }));
	}

	function getStyles(){
		$containerWidth = $this->context->containerWidth;
		$bgWidth = $this->getAttribute('background-width') ?: $containerWidth;
		$bgHeight = $this->getAttribute('background-height');
		$bgWidth2 = $this->getAttribute('background-width');

		$backgroundRatio = ($bgWidth2 && $bgHeight)
			? round((int)$bgHeight / (int)$bgWidth2 * 100)
			: 0;

		return [
			'div' => [
				'margin' => '0 auto',
				'max-width' => $containerWidth,
			],
			'table' => [
				'width' => '100%',
			],
			'tr' => [
				'vertical-align' => 'top',
			],
			'td-fluid' => [
				'width' => '0.01%',
				'padding-bottom' => "{$backgroundRatio}%",
				'mso-padding-bottom-alt' => '0',
			],
			'hero' => [
				'background' => $this->getBackground(),
				'background-position' => $this->getAttribute('background-position'),
				'background-repeat' => 'no-repeat',
				'border-radius' => $this->getAttribute('border-radius'),
				'padding' => $this->getAttribute('padding'),
				'padding-top' => $this->getAttribute('padding-top'),
				'padding-left' => $this->getAttribute('padding-left'),
				'padding-right' => $this->getAttribute('padding-right'),
				'padding-bottom' => $this->getAttribute('padding-bottom'),
				'vertical-align' => $this->getAttribute('vertical-align'),
			],
			'outlook-table' => [
				'width' => $containerWidth,
			],
			'outlook-td' => [
				'line-height' => 0,
				'font-size' => 0,
				'mso-line-height-rule' => 'exactly',
			],
			'outlook-inner-table' => [
				'width' => $containerWidth,
			],
			'outlook-image' => [
				'border' => '0',
				'height' => $bgHeight,
				'mso-position-horizontal' => 'center',
				'position' => 'absolute',
				'top' => 0,
				'width' => $bgWidth,
				'z-index' => '-3',
			],
			'outlook-inner-td' => [
				'background-color' => $this->getAttribute('inner-background-color'),
				'padding' => $this->getAttribute('inner-padding'),
				'padding-top' => $this->getAttribute('inner-padding-top'),
				'padding-left' => $this->getAttribute('inner-padding-left'),
				'padding-right' => $this->getAttribute('inner-padding-right'),
				'padding-bottom' => $this->getAttribute('inner-padding-bottom'),
			],
			'inner-table' => [
				'width' => '100%',
				'margin' => '0px',
			],
			'inner-div' => [
				'background-color' => $this->getAttribute('inner-background-color'),
				'float' => $this->getAttribute('align'),
				'margin' => '0px auto',
				'width' => $this->getAttribute('width'),
			],
		];
	}

	function renderContent(){
		$containerWidth = $this->context->containerWidth;
		$containerWidthPx = (int)$containerWidth;

		$hero = $this;
		$renderedChildren = $this->renderChildren(function($component) use ($hero){
			$tdAttrs = $component->htmlAttributes([
				'align' => $component->getAttribute('align'),
				'background' => $component->getAttribute('container-background-color'),
				'class' => $component->getAttribute('css-class'),
				'style' => [
					'background' => $component->getAttribute('container-background-color'),
					'font-size' => '0px',
					'padding' => $component->getAttribute('padding'),
					'padding-top' => $component->getAttribute('padding-top'),
					'padding-right' => $component->getAttribute('padding-right'),
					'padding-bottom' => $component->getAttribute('padding-bottom'),
					'padding-left' => $component->getAttribute('padding-left'),
					'word-break' => 'break-word',
				],
			]);
			return "<tr><td {$tdAttrs}>{$component->render()}</td></tr>";
		});

		$innerTableAttrs = $this->htmlAttributes([
			'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
			'role' => 'presentation', 'style' => 'inner-table',
		]);
		$outlookInnerTableAttrs = $this->htmlAttributes([
			'align' => $this->getAttribute('align'),
			'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
			'style' => 'outlook-inner-table', 'width' => $containerWidthPx,
		]);

		return "
		<!--[if mso | IE]>
		<table {$outlookInnerTableAttrs}>
			<tr>
				<td {$this->htmlAttributes(['style' => 'outlook-inner-td'])}>
		<![endif]-->
		<div {$this->htmlAttributes(['align' => $this->getAttribute('align'), 'class' => 'mj-hero-content', 'style' => 'inner-div'])}>
			<table {$innerTableAttrs}>
				<tbody>
					<tr>
						<td {$this->htmlAttributes(['style' => 'inner-td'])}>
							<table {$innerTableAttrs}>
								<tbody>
									{$renderedChildren}
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<!--[if mso | IE]>
				</td>
			</tr>
		</table>
		<![endif]-->
		";
	}

	function renderMode(){
		$commonAttrs = [
			'background' => $this->getAttribute('background-url'),
			'style' => 'hero',
		];

		if($this->getAttribute('mode') === 'fluid-height'){
			$magicTd = $this->htmlAttributes(['style' => 'td-fluid']);
			$tdAttrs = $this->htmlAttributes($commonAttrs);
			return "<td {$magicTd} /><td {$tdAttrs}>{$this->renderContent()}</td><td {$magicTd} />";
		}

		// fixed-height (default)
		$height = (int)$this->getAttribute('height')
				- $this->getShorthandAttrValue('padding', 'top')
				- $this->getShorthandAttrValue('padding', 'bottom');

		$tdAttrs = $this->htmlAttributes(array_merge($commonAttrs, [
			'height' => $height,
			'style' => array_merge(
				$this->getStyles()['hero'],
				['height' => "{$height}px"]
			),
		]));
		return "<td {$tdAttrs}>{$this->renderContent()}</td>";
	}

	function render(){
		$containerWidth = $this->context->containerWidth;
		$containerWidthPx = (int)$containerWidth;

		$outlookTableAttrs = $this->htmlAttributes([
			'align' => 'center',
			'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
			'role' => 'presentation', 'style' => 'outlook-table', 'width' => $containerWidthPx,
		]);
		$vimageAttrs = $this->htmlAttributes([
			'style' => 'outlook-image',
			'src' => $this->getAttribute('background-url'),
			'xmlns:v' => 'urn:schemas-microsoft-com:vml',
		]);
		$divAttrs = $this->htmlAttributes([
			'align' => $this->getAttribute('align'),
			'class' => $this->getAttribute('css-class'),
			'style' => 'div',
		]);
		$tableAttrs = $this->htmlAttributes([
			'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
			'role' => 'presentation', 'style' => 'table',
		]);
		$trAttrs = $this->htmlAttributes(['style' => 'tr']);

		return "
		<!--[if mso | IE]>
		<table {$outlookTableAttrs}>
			<tr>
				<td {$this->htmlAttributes(['style' => 'outlook-td'])}>
					<v:image {$vimageAttrs} />
		<![endif]-->
		<div {$divAttrs}>
			<table {$tableAttrs}>
				<tbody>
					<tr {$trAttrs}>
						{$this->renderMode()}
					</tr>
				</tbody>
			</table>
		</div>
		<!--[if mso | IE]>
				</td>
			</tr>
		</table>
		<![endif]-->
		";
	}
}
