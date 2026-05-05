<?php
namespace Yarri\Mjml\Tags;

class MjNavbar extends _Tag {

	static $componentName = 'mj-navbar';

	static $allowedAttributes = [
		'align' => 'enum(left,center,right)',
		'base-url' => 'string',
		'hamburger' => 'string',
		'ico-align' => 'enum(left,center,right)',
		'ico-open' => 'string',
		'ico-close' => 'string',
		'ico-color' => 'color',
		'ico-font-size' => 'unit(px,%)',
		'ico-font-family' => 'string',
		'ico-text-transform' => 'string',
		'ico-padding' => 'unit(px,%){1,4}',
		'ico-padding-left' => 'unit(px,%)',
		'ico-padding-top' => 'unit(px,%)',
		'ico-padding-right' => 'unit(px,%)',
		'ico-padding-bottom' => 'unit(px,%)',
		'padding' => 'unit(px,%){1,4}',
		'padding-left' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-bottom' => 'unit(px,%)',
		'ico-text-decoration' => 'string',
		'ico-line-height' => 'unit(px,%,)',
	];

	static $defaultAttributes = [
		'align' => 'center',
		'ico-align' => 'center',
		'ico-open' => '&#9776;',
		'ico-close' => '&#8855;',
		'ico-color' => '#000000',
		'ico-font-size' => '30px',
		'ico-font-family' => 'Ubuntu, Helvetica, Arial, sans-serif',
		'ico-text-transform' => 'uppercase',
		'ico-padding' => '10px',
		'ico-text-decoration' => 'none',
		'ico-line-height' => '30px',
	];

	function headStyle($breakpoint){
		return "
      noinput.mj-menu-checkbox { display:block!important; max-height:none!important; visibility:visible!important; }

      @media only screen and (max-width:{$breakpoint}) {
        .mj-menu-checkbox[type=\"checkbox\"] ~ .mj-inline-links { display:none!important; }
        .mj-menu-checkbox[type=\"checkbox\"]:checked ~ .mj-inline-links,
        .mj-menu-checkbox[type=\"checkbox\"] ~ .mj-menu-trigger { display:block!important; max-width:none!important; max-height:none!important; font-size:inherit!important; }
        .mj-menu-checkbox[type=\"checkbox\"] ~ .mj-inline-links > a { display:block!important; }
        .mj-menu-checkbox[type=\"checkbox\"]:checked ~ .mj-menu-trigger .mj-menu-icon-close { display:block!important; }
        .mj-menu-checkbox[type=\"checkbox\"]:checked ~ .mj-menu-trigger .mj-menu-icon-open { display:none!important; }
      }
    ";
	}

	function getStyles(){
		return [
			'div' => [
				'align' => $this->getAttribute('align'),
				'width' => '100%',
			],
			'label' => [
				'display' => 'block',
				'cursor' => 'pointer',
				'mso-hide' => 'all',
				'-moz-user-select' => 'none',
				'user-select' => 'none',
				'color' => $this->getAttribute('ico-color'),
				'font-size' => $this->getAttribute('ico-font-size'),
				'font-family' => $this->getAttribute('ico-font-family'),
				'text-transform' => $this->getAttribute('ico-text-transform'),
				'text-decoration' => $this->getAttribute('ico-text-decoration'),
				'line-height' => $this->getAttribute('ico-line-height'),
				'padding-top' => $this->getAttribute('ico-padding-top'),
				'padding-right' => $this->getAttribute('ico-padding-right'),
				'padding-bottom' => $this->getAttribute('ico-padding-bottom'),
				'padding-left' => $this->getAttribute('ico-padding-left'),
				'padding' => $this->getAttribute('ico-padding'),
			],
			'trigger' => [
				'display' => 'none',
				'max-height' => '0px',
				'max-width' => '0px',
				'font-size' => '0px',
				'overflow' => 'hidden',
			],
			'icoOpen' => ['mso-hide' => 'all'],
			'icoClose' => ['display' => 'none', 'mso-hide' => 'all'],
		];
	}

	function renderHamburger(){
		$key = bin2hex(random_bytes(8));
		$labelAttrs = $this->htmlAttributes([
			'for' => $key,
			'class' => 'mj-menu-label',
			'style' => 'label',
			'align' => $this->getAttribute('ico-align'),
		]);

		return "
		<!--[if !mso><!-->
		<input type=\"checkbox\" id=\"{$key}\" class=\"mj-menu-checkbox\" style=\"display:none !important; max-height:0; visibility:hidden;\" />
		<!--<![endif]-->
		<div {$this->htmlAttributes(['class' => 'mj-menu-trigger', 'style' => 'trigger'])}>
			<label {$labelAttrs}>
				<span {$this->htmlAttributes(['class' => 'mj-menu-icon-open', 'style' => 'icoOpen'])}>{$this->getAttribute('ico-open')}</span>
				<span {$this->htmlAttributes(['class' => 'mj-menu-icon-close', 'style' => 'icoClose'])}>{$this->getAttribute('ico-close')}</span>
			</label>
		</div>
		";
	}

	function render(){
		$align = $this->getAttribute('align');
		$baseUrl = $this->getAttribute('base-url');

		// Pass navbarBaseUrl attribute to each link child
		$navbar = $this;
		$renderedLinks = $this->renderChildren(function($component) use ($baseUrl){
			if($baseUrl){ $component->attributes['navbarBaseUrl'] = $baseUrl; }
			return $component->render();
		});

		// The JS reference outputs style="" for this div (bug in JS: calls htmlAttributes('div')
		// instead of styles('div'), which always returns empty string)
		$divAttrs = $this->htmlAttributes([
			'class' => 'mj-inline-links',
			'style' => '',
		]);

		$hamburger = $this->getAttribute('hamburger') === 'hamburger' ? $this->renderHamburger() : '';

		return "
		{$hamburger}
		<div {$divAttrs}>
			<!--[if mso | IE]>
			<table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"{$align}\">
				<tr>
			<![endif]-->
				{$renderedLinks}
			<!--[if mso | IE]>
				</tr>
			</table>
			<![endif]-->
		</div>
		";
	}
}
