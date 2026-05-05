<?php
namespace Yarri\Mjml\Tags;

class MjSocial extends _Tag {

	static $componentName = 'mj-social';

	static $allowedAttributes = [
		'align' => 'enum(left,right,center)',
		'border-radius' => 'unit(px,%)',
		'container-background-color' => 'color',
		'color' => 'color',
		'font-family' => 'string',
		'font-size' => 'unit(px)',
		'font-style' => 'string',
		'font-weight' => 'string',
		'icon-size' => 'unit(px,%)',
		'icon-height' => 'unit(px,%)',
		'icon-padding' => 'unit(px,%){1,4}',
		'inner-padding' => 'unit(px,%){1,4}',
		'line-height' => 'unit(px,%,)',
		'mode' => 'enum(horizontal,vertical)',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'padding' => 'unit(px,%){1,4}',
		'table-layout' => 'enum(auto,fixed)',
		'text-padding' => 'unit(px,%){1,4}',
		'text-decoration' => 'string',
		'vertical-align' => 'enum(top,bottom,middle)',
	];

	static $defaultAttributes = [
		'align' => 'center',
		'border-radius' => '3px',
		'color' => '#333333',
		'font-family' => 'Ubuntu, Helvetica, Arial, sans-serif',
		'font-size' => '13px',
		'icon-size' => '20px',
		'line-height' => '22px',
		'mode' => 'horizontal',
		'padding' => '10px 25px',
		'text-decoration' => 'none',
	];

	function getStyles(){
		return [
			'tableVertical' => [
				'margin' => '0px',
			],
		];
	}

	/** Attributes to pass down to each mj-social-element child */
	function getSocialElementAttributes(){
		$attrs = [];
		$innerPadding = $this->getAttribute('inner-padding');
		if($innerPadding){ $attrs['padding'] = $innerPadding; }

		foreach([
			'border-radius', 'color', 'font-family', 'font-size', 'font-weight',
			'font-style', 'icon-size', 'icon-height', 'icon-padding',
			'text-padding', 'line-height', 'text-decoration',
		] as $attr){
			$attrs[$attr] = $this->getAttribute($attr);
		}
		return $attrs;
	}

	function renderHorizontal(){
		$align = $this->getAttribute('align');
		$social = $this;

		// Merge parent attributes into each child (only as defaults)
		$parentAttrs = $this->getSocialElementAttributes();
		$wrappedChildren = $this->renderChildren(function($component) use ($align, $parentAttrs, $social){
			// Apply parent attrs as defaults (don't override explicitly set child attrs)
			foreach($parentAttrs as $attr => $val){
				if(!isset($component->attributes[$attr]) || is_null($component->attributes[$attr])){
					$component->attributes[$attr] = $val;
				}
			}
			$tableAttrs = $component->htmlAttributes([
				'align' => $align,
				'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
				'role' => 'presentation',
				'style' => ['float' => 'none', 'display' => 'inline-table'],
			]);
			return "
			<!--[if mso | IE]><td><![endif]-->
			<table {$tableAttrs}>
				<tbody>
					{$component->render()}
				</tbody>
			</table>
			<!--[if mso | IE]></td><![endif]-->
			";
		});

		$tableAttrs = $this->htmlAttributes([
			'align' => $align,
			'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
			'role' => 'presentation',
		]);

		return "
		<!--[if mso | IE]>
		<table {$tableAttrs}>
			<tr>
		<![endif]-->
			{$wrappedChildren}
		<!--[if mso | IE]>
			</tr>
		</table>
		<![endif]-->
		";
	}

	function renderVertical(){
		$parentAttrs = $this->getSocialElementAttributes();
		$social = $this;

		$renderedChildren = $this->renderChildren(function($component) use ($parentAttrs){
			foreach($parentAttrs as $attr => $val){
				if(!isset($component->attributes[$attr]) || is_null($component->attributes[$attr])){
					$component->attributes[$attr] = $val;
				}
			}
			return $component->render();
		});

		$tableAttrs = $this->htmlAttributes([
			'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
			'role' => 'presentation', 'style' => 'tableVertical',
		]);

		return "
		<table {$tableAttrs}>
			<tbody>
				{$renderedChildren}
			</tbody>
		</table>
		";
	}

	function render(){
		return $this->getAttribute('mode') === 'horizontal'
			? $this->renderHorizontal()
			: $this->renderVertical();
	}
}
