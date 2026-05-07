<?php
namespace Yarri\Mjml\Tags;

class MjSocialElement extends _Tag {

	static $componentName = 'mj-social-element';

	static $allowedAttributes = [
		'align' => 'enum(left,center,right)',
		'background-color' => 'color',
		'color' => 'color',
		'border-radius' => 'unit(px)',
		'font-family' => 'string',
		'font-size' => 'unit(px)',
		'font-style' => 'string',
		'font-weight' => 'string',
		'href' => 'string',
		'icon-size' => 'unit(px,%)',
		'icon-height' => 'unit(px,%)',
		'icon-padding' => 'unit(px,%){1,4}',
		'line-height' => 'unit(px,%,)',
		'name' => 'string',
		'padding-bottom' => 'unit(px,%)',
		'padding-left' => 'unit(px,%)',
		'padding-right' => 'unit(px,%)',
		'padding-top' => 'unit(px,%)',
		'padding' => 'unit(px,%){1,4}',
		'text-padding' => 'unit(px,%){1,4}',
		'rel' => 'string',
		'src' => 'string',
		'srcset' => 'string',
		'sizes' => 'string',
		'alt' => 'string',
		'title' => 'string',
		'target' => 'string',
		'text-decoration' => 'string',
		'vertical-align' => 'enum(top,middle,bottom)',
	];

	static $defaultAttributes = [
		'align' => 'left',
		'alt' => '',
		'color' => '#000',
		'border-radius' => '3px',
		'font-family' => 'Ubuntu, Helvetica, Arial, sans-serif',
		'font-size' => '13px',
		'line-height' => '1',
		'padding' => '4px',
		'text-padding' => '4px 4px 4px 0',
		'target' => '_blank',
		'text-decoration' => 'none',
		'vertical-align' => 'middle',
	];

	static $defaultSocialNetworks = [
		'facebook' => [
			'share-url' => 'https://www.facebook.com/sharer/sharer.php?u=[[URL]]',
			'background-color' => '#3b5998',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/facebook.png',
		],
		'twitter' => [
			'share-url' => 'https://twitter.com/intent/tweet?url=[[URL]]',
			'background-color' => '#55acee',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/twitter.png',
		],
		'google' => [
			'share-url' => 'https://plus.google.com/share?url=[[URL]]',
			'background-color' => '#dc4e41',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/google-plus.png',
		],
		'pinterest' => [
			'share-url' => 'https://pinterest.com/pin/create/button/?url=[[URL]]&media=&description=',
			'background-color' => '#bd081c',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/pinterest.png',
		],
		'linkedin' => [
			'share-url' => 'https://www.linkedin.com/shareArticle?mini=true&url=[[URL]]&title=&summary=&source=',
			'background-color' => '#0077b5',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/linkedin.png',
		],
		'instagram' => [
			'background-color' => '#3f729b',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/instagram.png',
		],
		'web' => [
			'background-color' => '#4BADE9',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/web.png',
		],
		'youtube' => [
			'background-color' => '#EB3323',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/youtube.png',
		],
		'github' => [
			'background-color' => '#000000',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/github.png',
		],
		'vimeo' => [
			'background-color' => '#53B4E7',
			'src' => 'https://www.mailjet.com/images/theme/v1/icons/ico-social/vimeo.png',
		],
	];

	function getSocialAttributes(){
		$name = $this->getAttribute('name');
		$network = isset(static::$defaultSocialNetworks[$name]) ? static::$defaultSocialNetworks[$name] : [];

		$href = $this->getAttribute('href');
		if($href && isset($network['share-url'])){
			$href = str_replace('[[URL]]', $href, $network['share-url']);
		}

		$attrs = ['icon-size', 'icon-height', 'srcset', 'sizes', 'src', 'background-color'];
		$result = ['href' => $href];
		foreach($attrs as $attr){
			$result[$attr] = $this->getAttribute($attr) ?: (isset($network[$attr]) ? $network[$attr] : null);
		}
		return $result;
	}

	function getStyles(){
		$socialAttrs = $this->getSocialAttributes();
		$iconSize = $socialAttrs['icon-size'];
		$iconHeight = $socialAttrs['icon-height'];
		$bgColor = $socialAttrs['background-color'];

		return [
			'td' => [
				'padding' => $this->getAttribute('padding'),
				'vertical-align' => $this->getAttribute('vertical-align'),
			],
			'table' => [
				'background' => $bgColor,
				'border-radius' => $this->getAttribute('border-radius'),
				'width' => $iconSize,
			],
			'icon' => [
				'padding' => $this->getAttribute('icon-padding'),
				'font-size' => '0',
				'height' => $iconHeight ?: $iconSize,
				'vertical-align' => 'middle',
				'width' => $iconSize,
			],
			'img' => [
				'border-radius' => $this->getAttribute('border-radius'),
				'display' => 'block',
			],
			'tdText' => [
				'vertical-align' => 'middle',
				'padding' => $this->getAttribute('text-padding'),
				'text-align' => $this->getAttribute('align'),
			],
			'text' => [
				'color' => $this->getAttribute('color'),
				'font-size' => $this->getAttribute('font-size'),
				'font-weight' => $this->getAttribute('font-weight'),
				'font-style' => $this->getAttribute('font-style'),
				'font-family' => $this->getAttribute('font-family'),
				'line-height' => $this->getAttribute('line-height'),
				'text-decoration' => $this->getAttribute('text-decoration'),
			],
		];
	}

	function render(){
		$socialAttrs = $this->getSocialAttributes();
		$href = $socialAttrs['href'];
		$src = $socialAttrs['src'];
		$iconSize = $socialAttrs['icon-size'];
		$iconHeight = $socialAttrs['icon-height'];
		$hasLink = (bool)$this->getAttribute('href');

		$imgAttrs = $this->htmlAttributes([
			'alt' => $this->getAttribute('alt'),
			'title' => $this->getAttribute('title'),
			'src' => $src,
			'style' => 'img',
			'width' => (int)$iconSize,
			'sizes' => $socialAttrs['sizes'],
			'srcset' => $socialAttrs['srcset'],
		]);

		$linkAttrs = $this->htmlAttributes([
			'href' => $href,
			'rel' => $this->getAttribute('rel'),
			'target' => $this->getAttribute('target'),
		]);

		$img = $hasLink
			? "<a {$linkAttrs}><img {$imgAttrs} /></a>"
			: "<img {$imgAttrs} />";

		$tableAttrs = $this->htmlAttributes([
			'border' => '0', 'cellpadding' => '0', 'cellspacing' => '0',
			'role' => 'presentation', 'style' => 'table',
		]);

		$textContent = $this->getContent();
		$textTd = '';
		if($textContent){
			if($hasLink){
				$textEl = "<a {$this->htmlAttributes(['href' => $href, 'style' => 'text', 'rel' => $this->getAttribute('rel'), 'target' => $this->getAttribute('target')])}>{$textContent}</a>";
			}else{
				$textEl = "<span {$this->htmlAttributes(['style' => 'text'])}>{$textContent}</span>";
			}
			$textTd = "<td {$this->htmlAttributes(['style' => 'tdText'])}>{$textEl}</td>";
		}

		return "
		<tr {$this->htmlAttributes(['class' => $this->getAttribute('css-class')])}>
			<td {$this->htmlAttributes(['style' => 'td'])}>
				<table {$tableAttrs}>
					<tbody>
						<tr>
							<td {$this->htmlAttributes(['style' => 'icon'])}>
								{$img}
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			{$textTd}
		</tr>
		";
	}
}
