<?php
namespace Yarri\Mjml\Tags;

class MjCarouselImage extends _Tag {

	static $componentName = 'mj-carousel-image';

	static $endingTag = true;

	static $allowedAttributes = [
		'alt' => 'string',
		'href' => 'string',
		'rel' => 'string',
		'target' => 'string',
		'title' => 'string',
		'src' => 'string',
		'thumbnails-src' => 'string',
		'border-radius' => 'unit(px,%){1,4}',
		'tb-border' => 'string',
		'tb-border-radius' => 'unit(px,%){1,4}',
	];

	static $defaultAttributes = [
		'target' => '_blank',
	];

	function getStyles(){
		$containerWidth = $this->context->containerWidth;
		$index = isset($this->props['index']) ? $this->props['index'] : 0;

		return [
			'images' => [
				'img' => [
					'border-radius' => $this->getAttribute('border-radius'),
					'display' => 'block',
					'width' => $containerWidth,
					'max-width' => '100%',
					'height' => 'auto',
				],
				'firstImageDiv' => [],
				'otherImageDiv' => [
					'display' => 'none',
					'mso-hide' => 'all',
				],
			],
			'radio' => [
				'input' => [
					'display' => 'none',
					'mso-hide' => 'all',
				],
			],
			'thumbnails' => [
				'a' => [
					'border' => $this->getAttribute('tb-border'),
					'border-radius' => $this->getAttribute('tb-border-radius'),
					'display' => 'inline-block',
					'overflow' => 'hidden',
					'width' => $this->getAttribute('tb-width'),
				],
				'img' => [
					'display' => 'block',
					'width' => '100%',
					'height' => 'auto',
				],
			],
		];
	}

	function renderRadio(){
		$index = isset($this->props['index']) ? $this->props['index'] : 0;
		$carouselId = $this->getAttribute('carouselId');

		return "
		<input {$this->htmlAttributes([
			'class' => "mj-carousel-radio mj-carousel-{$carouselId}-radio mj-carousel-{$carouselId}-radio-" . ($index + 1),
			'checked' => $index === 0 ? 'checked' : null,
			'type' => 'radio',
			'name' => "mj-carousel-radio-{$carouselId}",
			'id' => "mj-carousel-{$carouselId}-radio-" . ($index + 1),
			'style' => 'radio.input',
		])} />
		";
	}

	function renderThumbnail(){
		$index = isset($this->props['index']) ? $this->props['index'] : 0;
		$imgIndex = $index + 1;
		$carouselId = $this->getAttribute('carouselId');
		$src = $this->getAttribute('src');
		$alt = $this->getAttribute('alt');
		$tbWidth = $this->getAttribute('tb-width');
		$target = $this->getAttribute('target');
		$cssClass = $this->getAttribute('css-class') ? $this->suffixCssClasses($this->getAttribute('css-class'), 'thumbnail') : '';

		$aAttrs = $this->htmlAttributes([
			'style' => 'thumbnails.a',
			'href' => "#{$imgIndex}",
			'target' => $target,
			'class' => "mj-carousel-thumbnail mj-carousel-{$carouselId}-thumbnail mj-carousel-{$carouselId}-thumbnail-{$imgIndex} {$cssClass}",
		]);

		$labelAttrs = $this->htmlAttributes([
			'for' => "mj-carousel-{$carouselId}-radio-{$imgIndex}",
		]);

		$thumbSrc = $this->getAttribute('thumbnails-src') ?: $src;
		$imgAttrs = $this->htmlAttributes([
			'style' => 'thumbnails.img',
			'src' => $thumbSrc,
			'alt' => $alt,
			'width' => (int)$tbWidth,
		]);

		return "
		<a {$aAttrs}>
			<label {$labelAttrs}>
				<img {$imgAttrs} />
			</label>
		</a>
		";
	}

	function render(){
		$index = isset($this->props['index']) ? $this->props['index'] : 0;
		$src = $this->getAttribute('src');
		$alt = $this->getAttribute('alt');
		$href = $this->getAttribute('href');
		$rel = $this->getAttribute('rel');
		$title = $this->getAttribute('title');
		$containerWidth = $this->context->containerWidth;
		$cssClass = $this->getAttribute('css-class') ?: '';

		$imgAttrs = $this->htmlAttributes([
			'title' => $title,
			'src' => $src,
			'alt' => $alt,
			'style' => 'images.img',
			'width' => (int)$containerWidth,
			'border' => '0',
		]);

		$image = "<img {$imgAttrs} />";

		if($href){
			$aAttrs = $this->htmlAttributes([
				'href' => $href,
				'rel' => $rel,
				'target' => '_blank',
			]);
			$image = "<a {$aAttrs}>{$image}</a>";
		}

		$divStyle = $index === 0 ? 'images.firstImageDiv' : 'images.otherImageDiv';

		// JS always appends cssClass (even empty), producing a trailing space — match that behavior
		$divAttrs = $this->htmlAttributes([
			'class' => "mj-carousel-image mj-carousel-image-" . ($index + 1) . " {$cssClass}",
			'style' => $divStyle,
		]);

		return "
		<div {$divAttrs}>
			{$image}
		</div>
		";
	}
}
