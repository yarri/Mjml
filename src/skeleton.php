<?php
namespace Yarri\Mjml;

class Skeleton {

	static function render($options){
		$options += [
			'backgroundColor' => '',
			'breakpoint' => '480px',
			'content' => '',
			'fonts' => [],
			'mediaQueries' => [],
			'preview' => '',
			'style' => [],
			'title' => '',
		];

		$backgroundColor = $options['backgroundColor'];
		$breakpoint = $options['breakpoint'];
		$content = $options['content'];
		$fonts = $options['fonts'];
		$mediaQueries = $options['mediaQueries'];
		$preview = $options['preview'];
		$style = $options['style'];
		$title = $options['title'];

		$headStyle = $options['headStyle'] ?? [];
		$componentsHeadStyle = $options['componentsHeadStyle'] ?? [];

		$fontsTags = self::buildFontsTags($content, $fonts);
		$mediaQueriesTags = self::buildMediaQueriesTags($breakpoint, $mediaQueries);
		$previewHtml = self::buildPreview($preview);
		$headStyleCss = '';
		foreach($headStyle as $callable){
			$headStyleCss .= "\n" . call_user_func($callable, $breakpoint);
		}
		foreach($componentsHeadStyle as $callable){
			$headStyleCss .= "\n" . call_user_func($callable);
		}
		$customStyle = $style ? join("\n    ", $style) : '';
		$bodyStyle = 'word-spacing:normal;' . ($backgroundColor ? "background-color:{$backgroundColor};" : '');

		return <<<HTML
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <title>
      {$title}
    </title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
      #outlook a { padding:0; }
      body { margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; }
      table, td { border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt; }
      img { border:0;height:auto;line-height:100%; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic; }
      p { display:block;margin:13px 0; }
    </style>
    <!--[if mso]>
    <noscript>
    <xml>
    <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <!--[if lte mso 11]>
    <style type="text/css">
      .mj-outlook-group-fix { width:100% !important; }
    </style>
    <![endif]-->
    {$fontsTags}
    {$mediaQueriesTags}
    <style type="text/css">
    {$headStyleCss}
    </style>
    <style type="text/css">
    {$customStyle}
    </style>
  </head>
  <body style="{$bodyStyle}">
    {$previewHtml}
    {$content}
  </body>
</html>
HTML;
	}

	static function buildFontsTags($content, $fonts){
		if(empty($fonts)){ return ''; }

		$toImport = [];
		foreach($fonts as $name => $url){
			$nameQ = preg_quote($name, '/');
			// Check if font is used in a font-family style attribute value
			if(preg_match('/"[^"]*font-family:[^"]*' . $nameQ . '[^"]*"/im', $content)){
				$toImport[] = $url;
			}
		}

		if(empty($toImport)){ return ''; }

		$links = join("\n        ", array_map(function($url){
			return "<link href=\"{$url}\" rel=\"stylesheet\" type=\"text/css\">";
		}, $toImport));
		$imports = join("\n          ", array_map(function($url){
			return "@import url({$url});";
		}, $toImport));

		return <<<HTML

    <!--[if !mso]><!-->
      {$links}
      <style type="text/css">
        {$imports}
      </style>
    <!--<![endif]-->
HTML;
	}

	static function buildMediaQueriesTags($breakpoint, $mediaQueries){
		if(empty($mediaQueries)){ return ''; }

		$baseMediaQueries = [];
		$thunderbirdMediaQueries = [];
		foreach($mediaQueries as $className => $mediaQuery){
			$baseMediaQueries[] = ".{$className} {$mediaQuery}";
			$thunderbirdMediaQueries[] = ".moz-text-html .{$className} {$mediaQuery}";
		}

		$baseMQ = join("\n        ", $baseMediaQueries);
		$thunderbirdMQ = join("\n      ", $thunderbirdMediaQueries);

		return <<<HTML

    <style type="text/css">
      @media only screen and (min-width:{$breakpoint}) {
        {$baseMQ}
      }
    </style>
    <style media="screen and (min-width:{$breakpoint})">
      {$thunderbirdMQ}
    </style>
HTML;
	}

	static function buildPreview($preview){
		if(!$preview){ return ''; }
		return '<div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">'
			. $preview
			. '</div>';
	}

	static function mergeOutlookConditionals($content){
		return preg_replace('/(<!\[endif]-->\s*?<!--\[if mso \| IE]>)/m', '', $content);
	}

	static function minifyOutlookConditionals($content){
		return preg_replace_callback(
			'/(<!--\[if\s[^\]]+\]>)([\s\S]*?)(<!\[endif]-->)/m',
			function($matches){
				$prefix = $matches[1];
				$inner = $matches[2];
				$suffix = $matches[3];
				$inner = preg_replace('/(^|>)(\s+)(<|$)/', '$1$3', $inner);
				$inner = preg_replace('/\s{2,}/', ' ', $inner);
				return $prefix . $inner . $suffix;
			},
			$content
		);
	}
}
