<?php
namespace Yarri\Mjml\Tags;

class MjNavbarLink extends _Tag {

	static $componentName = 'mj-navbar-link';

	static $allowedAttributes = [
		'color' => 'color',
		'font-family' => 'string',
		'font-size' => 'unit(px)',
		'font-style' => 'string',
		'font-weight' => 'string',
		'href' => 'string',
		'name' => 'string',
		'target' => 'string',
		'rel' => 'string',
		'letter-spacing' => 'unitWithNegative(px,em)',
		'line-height' => 'unit(px,%,)',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'padding' => 'unit(px,%){1,4}',
		'text-decoration' => 'string',
		'text-transform' => 'string',
	];

	static $defaultAttributes = [
		'color' => '#000000',
		'font-family' => 'Ubuntu, Helvetica, Arial, sans-serif',
		'font-size' => '13px',
		'font-weight' => 'normal',
		'line-height' => '22px',
		'padding' => '15px 10px',
		'target' => '_blank',
		'text-decoration' => 'none',
		'text-transform' => 'uppercase',
	];

	function getStyles(){
		return [
			'a' => [
				'display' => 'inline-block',
				'color' => $this->getAttribute('color'),
				'font-family' => $this->getAttribute('font-family'),
				'font-size' => $this->getAttribute('font-size'),
				'font-style' => $this->getAttribute('font-style'),
				'font-weight' => $this->getAttribute('font-weight'),
				'letter-spacing' => $this->getAttribute('letter-spacing'),
				'line-height' => $this->getAttribute('line-height'),
				'text-decoration' => $this->getAttribute('text-decoration'),
				'text-transform' => $this->getAttribute('text-transform'),
				'padding' => $this->getAttribute('padding'),
				'padding-top' => $this->getAttribute('padding-top'),
				'padding-left' => $this->getAttribute('padding-left'),
				'padding-right' => $this->getAttribute('padding-right'),
				'padding-bottom' => $this->getAttribute('padding-bottom'),
			],
			'td' => [
				'padding' => $this->getAttribute('padding'),
				'padding-top' => $this->getAttribute('padding-top'),
				'padding-left' => $this->getAttribute('padding-left'),
				'padding-right' => $this->getAttribute('padding-right'),
				'padding-bottom' => $this->getAttribute('padding-bottom'),
			],
		];
	}

	function render(){
		$href = $this->getAttribute('href');
		$navbarBaseUrl = $this->getAttribute('navbarBaseUrl');
		$link = ($navbarBaseUrl && $href) ? $navbarBaseUrl . $href : $href;
		$cssClass = $this->getAttribute('css-class') ? ' ' . $this->getAttribute('css-class') : '';

		$aAttrs = $this->htmlAttributes([
			'class' => 'mj-link' . $cssClass,
			'href' => $link,
			'rel' => $this->getAttribute('rel'),
			'name' => $this->getAttribute('name'),
			'target' => $this->getAttribute('target'),
			'style' => 'a',
		]);

		return "
		<!--[if mso | IE]>
		<td {$this->htmlAttributes(['style' => 'td'])}>
		<![endif]-->
			<a {$aAttrs}>{$this->getContent()}</a>
		<!--[if mso | IE]>
		</td>
		<![endif]-->
		";
	}
}
