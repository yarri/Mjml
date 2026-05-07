<?php
namespace Yarri\Mjml\Tags;

class MjCarousel extends _Tag {

	static $componentName = 'mj-carousel';

	static $allowedAttributes = [
		'align' => 'enum(left,center,right)',
		'border-radius' => 'unit(px,%)',
		'container-background-color' => 'color',
		'icon-width' => 'unit(px,%)',
		'left-icon' => 'string',
		'padding' => 'unit(px,%){1,4}',
		'padding-top' => 'unit(px,%)',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'right-icon' => 'string',
		'thumbnails' => 'enum(visible,hidden)',
		'tb-border' => 'string',
		'tb-border-radius' => 'unit(px,%)',
		'tb-hover-border-color' => 'color',
		'tb-selected-border-color' => 'color',
		'tb-width' => 'unit(px,%)',
	];

	static $defaultAttributes = [
		'align' => 'center',
		'border-radius' => '6px',
		'icon-width' => '44px',
		'left-icon' => 'https://i.imgur.com/xTh3hln.png',
		'right-icon' => 'https://i.imgur.com/os7o9kz.png',
		'thumbnails' => 'visible',
		'tb-border' => '2px solid transparent',
		'tb-border-radius' => '6px',
		'tb-hover-border-color' => '#fead0d',
		'tb-selected-border-color' => '#ccc',
	];

	public $carouselId;

	function __construct($params = []){
		parent::__construct($params);
		$this->carouselId = bin2hex(random_bytes(6));
	}

	function componentHeadStyle(){
		$children = $this->props['children'];
		$length = count($children);
		if(!$length) return '';

		$carouselId = $this->carouselId;
		$iconWidth = $this->getAttribute('icon-width');

		// Generate CSS selectors for "hide all images"
		$hideAllSelectors = [];
		for($i = 0; $i < $length; $i++){
			$hideAllSelectors[] = ".mj-carousel-{$carouselId}-radio:checked " . str_repeat('+ * ', $i) . "+ .mj-carousel-content .mj-carousel-image";
		}
		$hideAllSelector = implode(",\n    ", $hideAllSelectors);

		// Generate CSS selectors for "show active image"
		$showActiveSelectors = [];
		for($i = 0; $i < $length; $i++){
			$showActiveSelectors[] = ".mj-carousel-{$carouselId}-radio-" . ($i + 1) . ":checked " . str_repeat('+ * ', $length - $i - 1) . "+ .mj-carousel-content .mj-carousel-image-" . ($i + 1);
		}
		$showActiveSelector = implode(",\n    ", $showActiveSelectors);

		// Generate CSS selectors for nav arrows and thumbnails
		$navSelectorsNext = [];
		$navSelectorsPrev = [];
		for($i = 0; $i < $length; $i++){
			$nextIdx = ($i + 1) % $length + 1;
			$prevIdx = (($i - 1) % $length + $length) % $length + 1;
			$navSelectorsNext[] = ".mj-carousel-{$carouselId}-radio-" . ($i + 1) . ":checked " . str_repeat('+ * ', $length - $i - 1) . "+ .mj-carousel-content .mj-carousel-next-{$nextIdx}";
			$navSelectorsPrev[] = ".mj-carousel-{$carouselId}-radio-" . ($i + 1) . ":checked " . str_repeat('+ * ', $length - $i - 1) . "+ .mj-carousel-content .mj-carousel-previous-{$prevIdx}";
		}
		$navSelectors = implode(",\n    ", array_merge($navSelectorsNext, $navSelectorsPrev));

		// Generate CSS selectors for selected thumbnail border
		$thumbSelectors = [];
		for($i = 0; $i < $length; $i++){
			$thumbSelectors[] = ".mj-carousel-{$carouselId}-radio-" . ($i + 1) . ":checked " . str_repeat('+ * ', $length - $i - 1) . "+ .mj-carousel-content .mj-carousel-{$carouselId}-thumbnail-" . ($i + 1);
		}
		$thumbSelector = implode(",\n    ", $thumbSelectors);

		$selectedBorderColor = $this->getAttribute('tb-selected-border-color');
		$hoverBorderColor = $this->getAttribute('tb-hover-border-color');

		// Hover selectors
		$hoverHideSelectors = [];
		for($i = 0; $i < $length; $i++){
			$hoverHideSelectors[] = ".mj-carousel-{$carouselId}-thumbnail:hover " . str_repeat('+ * ', $length - $i - 1) . "+ .mj-carousel-main .mj-carousel-image";
		}
		$hoverHideSelector = implode(",\n    ", $hoverHideSelectors);

		$hoverShowSelectors = [];
		for($i = 0; $i < $length; $i++){
			$hoverShowSelectors[] = ".mj-carousel-{$carouselId}-thumbnail-" . ($i + 1) . ":hover " . str_repeat('+ * ', $length - $i - 1) . "+ .mj-carousel-main .mj-carousel-image-" . ($i + 1);
		}
		$hoverShowSelector = implode(",\n    ", $hoverShowSelectors);

		$radioFallback = ".mj-carousel-{$carouselId}-radio-1:checked " . str_repeat('+ *', $length - 1) . " + .mj-carousel-content .mj-carousel-{$carouselId}-thumbnail-1";

		return "
    .mj-carousel {
      -webkit-user-select: none;
      -moz-user-select: none;
      user-select: none;
    }

    .mj-carousel-{$carouselId}-icons-cell {
      display: table-cell !important;
      width: {$iconWidth} !important;
    }

    .mj-carousel-radio,
    .mj-carousel-next,
    .mj-carousel-previous {
      display: none !important;
    }

    .mj-carousel-thumbnail,
    .mj-carousel-next,
    .mj-carousel-previous {
      touch-action: manipulation;
    }

    {$hideAllSelector} {
      display: none !important;
    }

    {$showActiveSelector} {
      display: block !important;
    }

    .mj-carousel-previous-icons,
    .mj-carousel-next-icons,
    {$navSelectors} {
      display: block !important;
    }

    {$thumbSelector} {
      border-color: {$selectedBorderColor} !important;
    }

    .mj-carousel-image img + div,
    .mj-carousel-thumbnail img + div {
      display: none !important;
    }

    {$hoverHideSelector} {
      display: none !important;
    }

    .mj-carousel-thumbnail:hover {
      border-color: {$hoverBorderColor} !important;
    }

    {$hoverShowSelector} {
      display: block !important;
    }

    .mj-carousel noinput { display:block !important; }
    .mj-carousel noinput .mj-carousel-image-1 { display: block !important; }
    .mj-carousel noinput .mj-carousel-arrows,
    .mj-carousel noinput .mj-carousel-thumbnails { display: none !important; }

    [owa] .mj-carousel-thumbnail { display: none !important; }

    @media screen yahoo {
      .mj-carousel-{$carouselId}-icons-cell,
      .mj-carousel-previous-icons,
      .mj-carousel-next-icons {
        display: none !important;
      }

      {$radioFallback} {
        border-color: transparent;
      }
    }
    ";
	}

	function getStyles(){
		return [
			'carousel' => [
				'div' => [
					'display' => 'table',
					'width' => '100%',
					'table-layout' => 'fixed',
					'text-align' => 'center',
					'font-size' => '0px',
				],
				'table' => [
					'caption-side' => 'top',
					'display' => 'table-caption',
					'table-layout' => 'fixed',
					'width' => '100%',
				],
			],
			'images' => [
				'td' => [
					'padding' => '0px',
				],
			],
			'controls' => [
				'div' => [
					'display' => 'none',
					'mso-hide' => 'all',
				],
				'img' => [
					'display' => 'block',
					'width' => $this->getAttribute('icon-width'),
					'height' => 'auto',
				],
				'td' => [
					'font-size' => '0px',
					'display' => 'none',
					'mso-hide' => 'all',
					'padding' => '0px',
				],
			],
		];
	}

	function thumbnailsWidth(){
		$children = $this->props['children'];
		$n = count($children);
		if(!$n) return '0px';
		$tbWidth = $this->getAttribute('tb-width');
		if($tbWidth) return $tbWidth;
		$containerWidth = (int)$this->context->containerWidth;
		return min([$containerWidth / $n, 110]) . 'px';
	}

	function generateRadios(){
		$carouselId = $this->carouselId;
		$index = 0;
		return $this->renderChildren(function($component) use ($carouselId, &$index){
			$component->props['index'] = $index;
			$component->attributes['carouselId'] = $carouselId;
			$index++;
			return $component->renderRadio();
		});
	}

	function generateThumbnails(){
		if($this->getAttribute('thumbnails') !== 'visible') return '';

		$carouselId = $this->carouselId;
		$tbBorder = $this->getAttribute('tb-border');
		$tbBorderRadius = $this->getAttribute('tb-border-radius');
		$tbWidth = $this->thumbnailsWidth();

		$index = 0;
		return $this->renderChildren(function($component) use ($carouselId, $tbBorder, $tbBorderRadius, $tbWidth, &$index){
			$component->props['index'] = $index;
			$component->attributes = array_merge([
				'tb-border' => $tbBorder,
				'tb-border-radius' => $tbBorderRadius,
				'tb-width' => $tbWidth,
				'carouselId' => $carouselId,
			], $component->attributes);
			$index++;
			return $component->renderThumbnail();
		});
	}

	function generateControls($direction, $icon){
		$carouselId = $this->carouselId;
		$iconWidth = (int)$this->getAttribute('icon-width');
		$children = $this->props['children'];
		$n = count($children);

		$labels = '';
		for($i = 1; $i <= $n; $i++){
			$labelAttrs = $this->htmlAttributes([
				'for' => "mj-carousel-{$carouselId}-radio-{$i}",
				'class' => "mj-carousel-{$direction} mj-carousel-{$direction}-{$i}",
			]);
			$imgAttrs = $this->htmlAttributes([
				'src' => $icon,
				'alt' => $direction,
				'style' => 'controls.img',
				'width' => $iconWidth,
			]);
			$labels .= "\n\t\t\t<label {$labelAttrs}><img {$imgAttrs} /></label>";
		}

		return "
		<td {$this->htmlAttributes(['class' => "mj-carousel-{$carouselId}-icons-cell", 'style' => 'controls.td'])}>
			<div {$this->htmlAttributes(['class' => "mj-carousel-{$direction}-icons", 'style' => 'controls.div'])}>
				{$labels}
			</div>
		</td>
		";
	}

	function generateImages(){
		$carouselId = $this->carouselId;
		$borderRadius = $this->getAttribute('border-radius');

		$index = 0;
		$renderedChildren = $this->renderChildren(function($component) use ($carouselId, $borderRadius, &$index){
			$component->props['index'] = $index;
			$component->attributes = array_merge([
				'border-radius' => $borderRadius,
			], $component->attributes);
			$index++;
			return $component->render();
		});

		return "
		<td {$this->htmlAttributes(['style' => 'images.td'])}>
			<div {$this->htmlAttributes(['class' => 'mj-carousel-images'])}>
				{$renderedChildren}
			</div>
		</td>
		";
	}

	function generateCarousel(){
		$tableAttrs = $this->htmlAttributes([
			'style' => 'carousel.table',
			'border' => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
			'width' => '100%',
			'role' => 'presentation',
			'class' => 'mj-carousel-main',
		]);

		return "
		<table {$tableAttrs}>
			<tbody>
				<tr>
					{$this->generateControls('previous', $this->getAttribute('left-icon'))}
					{$this->generateImages()}
					{$this->generateControls('next', $this->getAttribute('right-icon'))}
				</tr>
			</tbody>
		</table>
		";
	}

	function renderFallback(){
		$children = $this->props['children'];
		if(!count($children)) return '';

		$first = $children[0];
		$first->props['index'] = 0;
		$first->attributes = array_merge([
			'border-radius' => $this->getAttribute('border-radius'),
		], $first->attributes);

		$content = $first->render();

		return "<!--[if mso]>
		{$content}
		<![endif]-->";
	}

	function render(){
		$carouselId = $this->carouselId;

		$outerDivAttrs = $this->htmlAttributes([
			'class' => 'mj-carousel',
		]);

		$innerDivAttrs = $this->htmlAttributes([
			'class' => "mj-carousel-content mj-carousel-{$carouselId}-content",
			'style' => 'carousel.div',
		]);

		return "
		<!--[if !mso><!-->
		<div {$outerDivAttrs}>
			{$this->generateRadios()}
			<div {$innerDivAttrs}>
				{$this->generateThumbnails()}
				{$this->generateCarousel()}
			</div>
		</div>
		<!--<![endif]-->
		{$this->renderFallback()}
		";
	}
}
