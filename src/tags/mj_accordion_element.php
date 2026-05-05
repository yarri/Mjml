<?php
namespace Yarri\Mjml\Tags;

class MjAccordionElement extends _Tag {

	static $componentName = 'mj-accordion-element';

	static $allowedAttributes = [
		'background-color' => 'color',
		'border' => 'string',
		'font-family' => 'string',
		'icon-align' => 'enum(top,middle,bottom)',
		'icon-width' => 'unit(px,%)',
		'icon-height' => 'unit(px,%)',
		'icon-wrapped-url' => 'string',
		'icon-wrapped-alt' => 'string',
		'icon-unwrapped-url' => 'string',
		'icon-unwrapped-alt' => 'string',
		'icon-position' => 'enum(left,right)',
	];

	static $defaultAttributes = [];

	function getStyles(){
		return [
			'td' => [
				'padding' => '0px',
				'background-color' => $this->getAttribute('background-color'),
			],
			'label' => [
				'font-size' => '13px',
				'font-family' => $this->getAttribute('font-family'),
			],
			'input' => [
				'display' => 'none',
			],
		];
	}

	function handleMissingChildren($childrenAttrs){
		$children = $this->props['children'];
		$hasTitle = false;
		$hasText = false;
		foreach($children as $child){
			if($child::$componentName === 'mj-accordion-title') $hasTitle = true;
			if($child::$componentName === 'mj-accordion-text') $hasText = true;
		}

		$result = [];

		if(!$hasTitle){
			$titleTag = new MjAccordionTitle(['attributes' => $childrenAttrs]);
			$titleTag->context = $this->getChildContext();
			$result[] = $titleTag->render();
		}

		$result[] = $this->renderChildren(function($component) use ($childrenAttrs){
			$component->attributes = array_merge($childrenAttrs, $component->attributes);
			return $component->render();
		});

		if(!$hasText){
			$textTag = new MjAccordionText(['attributes' => $childrenAttrs]);
			$textTag->context = $this->getChildContext();
			$result[] = $textTag->render();
		}

		return implode("\n", array_filter($result, function($v){ return strlen(trim($v)) > 0; }));
	}

	function render(){
		$childrenAttrs = [];
		foreach(['border', 'icon-align', 'icon-width', 'icon-height', 'icon-position', 'icon-wrapped-url', 'icon-wrapped-alt', 'icon-unwrapped-url', 'icon-unwrapped-alt'] as $attr){
			$childrenAttrs[$attr] = $this->getAttribute($attr);
		}

		$trAttrs = $this->htmlAttributes([
			'class' => $this->getAttribute('css-class'),
		]);

		$labelAttrs = $this->htmlAttributes([
			'class' => 'mj-accordion-element',
			'style' => 'label',
		]);

		$inputAttrs = $this->htmlAttributes([
			'class' => 'mj-accordion-checkbox',
			'type' => 'checkbox',
			'style' => 'input',
		]);

		$children = $this->handleMissingChildren($childrenAttrs);

		return "
		<tr {$trAttrs}>
			<td {$this->htmlAttributes(['style' => 'td'])}>
				<label {$labelAttrs}>
					<!--[if !mso | IE]><!-->
					<input {$inputAttrs} />
					<!--<![endif]-->
					<div>
						{$children}
					</div>
				</label>
			</td>
		</tr>
		";
	}
}
