<?php
namespace Yarri\Mjml;

class Parser {

	protected $head;
	protected $body;

	function __construct(\XMole $head, \XMole $body){
		$this->head = $head;
		$this->body = $body;
	}

	function parse(){
		$globalData = new \Yarri\Mjml\GlobalData();
		$this->_processHead($this->head, $globalData);
		$body_tag = $this->_buildTag($this->body, null, 1, $globalData);
		$out = $body_tag->render();

		// Remove newlines and extra whitespace inside HTML tags
		$out = preg_replace_callback(
			'/(<.*?>)/s',
			function($matches){
				$tag = $matches[1];
				$tag = preg_replace('/[ \t]*\n[ \t]*/', ' ', $tag);
				$tag = preg_replace('/ >$/', '>', $tag);
				return $tag;
			},
			$out
		);

		$out = \Yarri\Mjml\Skeleton::minifyOutlookConditionals($out);

		$out = \Yarri\Mjml\Skeleton::render([
			'content' => $out,
			'backgroundColor' => $globalData->backgroundColor,
			'breakpoint' => $globalData->breakpoint,
			'mediaQueries' => $globalData->mediaQueries,
			'title' => $globalData->title,
			'preview' => $globalData->preview,
			'fonts' => $globalData->fonts,
			'style' => $globalData->style,
			'headStyle' => $globalData->headStyle,
			'componentsHeadStyle' => $globalData->componentsHeadStyle,
		]);

		$out = \Yarri\Mjml\Skeleton::mergeOutlookConditionals($out);

		// Apply mj-html-attributes: inject custom HTML attributes by CSS selector
		if(!empty($globalData->htmlAttributes)){
			$out = $this->_applyHtmlAttributes($out, $globalData->htmlAttributes);
		}

		return $out;
	}

	/**
	 * Process mj-head children and populate globalData.
	 */
	function _processHead(\XMole $head, \Yarri\Mjml\GlobalData $globalData){
		foreach($head->get_children() as $child){
			$tag_name = $child->get_root_name();
			$attrs = $child->get_root_attributes();

			switch($tag_name){
				case 'mj-title':
					$globalData->title = trim($this->_getElementContent($child));
					break;

				case 'mj-preview':
					$globalData->preview = trim($this->_getElementContent($child));
					break;

				case 'mj-font':
					if(isset($attrs['name']) && isset($attrs['href'])){
						$globalData->fonts[$attrs['name']] = $attrs['href'];
					}
					break;

				case 'mj-breakpoint':
					if(isset($attrs['width'])){
						$globalData->breakpoint = $attrs['width'];
					}
					break;

				case 'mj-style':
					$css = $this->_getElementContent($child);
					$globalData->style[] = $css;
					break;

				case 'mj-attributes':
					foreach($child->get_children() as $attrChild){
						$childTag = $attrChild->get_root_name();
						$childAttrs = $attrChild->get_root_attributes();
						if($childTag === 'mj-all'){
							$globalData->defaultAttributes['mj-all'] = array_merge(
								isset($globalData->defaultAttributes['mj-all']) ? $globalData->defaultAttributes['mj-all'] : [],
								$childAttrs
							);
						}elseif($childTag === 'mj-class'){
							$className = isset($childAttrs['name']) ? $childAttrs['name'] : null;
							if($className){
								unset($childAttrs['name']);
								$globalData->classAttributes[$className] = array_merge(
									isset($globalData->classAttributes[$className]) ? $globalData->classAttributes[$className] : [],
									$childAttrs
								);
							}
						}else{
							$globalData->defaultAttributes[$childTag] = array_merge(
								isset($globalData->defaultAttributes[$childTag]) ? $globalData->defaultAttributes[$childTag] : [],
								$childAttrs
							);
						}
					}
					break;

				case 'mj-html-attributes':
					foreach($child->get_children() as $selectorEl){
						if($selectorEl->get_root_name() !== 'mj-selector') break;
						$selectorAttrs = $selectorEl->get_root_attributes();
						$path = isset($selectorAttrs['path']) ? $selectorAttrs['path'] : null;
						if(!$path) break;
						$customAttrs = [];
						foreach($selectorEl->get_children() as $attrEl){
							if($attrEl->get_root_name() !== 'mj-html-attribute') break;
							$attrDef = $attrEl->get_root_attributes();
							$name = isset($attrDef['name']) ? $attrDef['name'] : null;
							if(!$name) break;
							$value = $this->_getElementContent($attrEl);
							$customAttrs[$name] = $value;
						}
						if($customAttrs){
							$globalData->htmlAttributes[$path] = array_merge(
								isset($globalData->htmlAttributes[$path]) ? $globalData->htmlAttributes[$path] : [],
								$customAttrs
							);
						}
					}
					break;
			}
		}
	}

	/**
	 * Apply custom HTML attributes to elements matching CSS class selectors.
	 * Supports: .classname, .class1.class2 (AND), .class1 .class2 (descendant — simplified)
	 *
	 * @param string $html  Full rendered HTML document
	 * @param array  $htmlAttributes  map[cssSelector => [attrName => value]]
	 * @return string  Modified HTML
	 */
	function _applyHtmlAttributes($html, $htmlAttributes){
		$doc = new \DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();
		$xpath = new \DOMXPath($doc);

		foreach($htmlAttributes as $selector => $attrs){
			$xpathExpr = $this->_cssToXPath(trim($selector));
			if(!$xpathExpr) continue;
			$nodes = $xpath->query($xpathExpr);
			if(!$nodes) continue;
			foreach($nodes as $node){
				foreach($attrs as $attrName => $attrValue){
					$node->setAttribute($attrName, (string)$attrValue);
				}
			}
		}

		return $doc->saveHTML();
	}

	/**
	 * Convert a simple CSS selector to XPath expression.
	 * Supports: .class, .class1.class2 (AND), .a .b (descendant)
	 */
	function _cssToXPath($selector){
		// Handle descendant selectors: ".a .b" → //*[...a...]//*[...b...]
		$parts = preg_split('/\s+/', $selector);
		$xpathParts = [];
		foreach($parts as $part){
			if(!strlen($part)) continue;
			// Parse class conditions from e.g. ".foo.bar"
			$classes = array_filter(explode('.', $part));
			if(empty($classes)) continue;
			$conditions = array_map(function($cls){
				return "contains(concat(' ', normalize-space(@class), ' '), ' {$cls} ')";
			}, $classes);
			$xpathParts[] = '//*[' . implode(' and ', $conditions) . ']';
		}
		if(empty($xpathParts)) return null;
		return implode('', $xpathParts);
	}

	/**
	 * Extract inner text/HTML content from an XMole element (strips the outer tag).
	 */
	function _getElementContent(\XMole $element){
		$content = (string)$element;
		$content = preg_replace('/^<[^>]+>/s', '', $content);
		$content = preg_replace('/<[^>]+>$/s', '', $content);
		return $content;
	}

	/**
	 * Recursively build a tag object tree with proper context propagation.
	 *
	 * @param \XMole $element
	 * @param object|null $context  Context object (with containerWidth) from parent
	 * @param int $nonRawSiblings   How many mj-* siblings this element has (including itself)
	 * @return \Yarri\Mjml\Tags\_Tag
	 */
	function _buildTag(\XMole $element, $context = null, $nonRawSiblings = 1, $globalData = null){
		$tag_name = $element->get_root_name();
		$attributes = $element->get_root_attributes();

		// Apply global default attributes from mj-attributes (explicit attrs take precedence)
		// Priority: mj-all < mj-class < component-type defaults < explicit attributes
		$gd = $globalData !== null ? $globalData : ($context !== null && isset($context->globalData) ? $context->globalData : null);
		if($gd !== null){
			$globalAttrs = isset($gd->defaultAttributes['mj-all']) ? $gd->defaultAttributes['mj-all'] : [];
			$tagAttrs = isset($gd->defaultAttributes[$tag_name]) ? $gd->defaultAttributes[$tag_name] : [];
			$classAttrs = [];
			if(isset($attributes['mj-class'])){
				foreach(preg_split('/\s+/', trim($attributes['mj-class'])) as $cls){
					if($cls !== '' && isset($gd->classAttributes[$cls])){
						$classAttrs = array_merge($classAttrs, $gd->classAttributes[$cls]);
					}
				}
			}
			$attributes = array_merge($globalAttrs, $tagAttrs, $classAttrs, $attributes);
			unset($attributes['mj-class']);
		}

		// Find mj-* children
		$children_elements = $element->get_children();
		$children_elements = array_filter($children_elements, function($child){
			return (bool)preg_match('/^mj-/', $child->get_root_name());
		});
		$children_elements = array_values($children_elements);

		// Instantiate tag object
		$tag_class = \String4::ToObject($tag_name)->replace('-', '_')->camelize()->toString();
		$tag_class = "Yarri\\Mjml\\Tags\\$tag_class";

		$tag_obj = new $tag_class([
			"content" => "",
			"attributes" => $attributes,
		]);

		// Propagate context from parent
		if($context !== null){
			$tag_obj->context = $context;
		} elseif($globalData !== null){
			// Root node: attach globalData to its fresh context
			$tag_obj->context->globalData = $globalData;
		}

		// Register component headStyle (e.g. navbar hamburger CSS) — deduplicated per type
		if(method_exists($tag_obj, 'headStyle') && isset($tag_obj->context->globalData)){
			$tag_obj->context->globalData->addHeadStyle($tag_name, [$tag_obj, 'headStyle']);
		}
		// Register per-instance componentHeadStyle (e.g. carousel — each instance has unique CSS)
		if(method_exists($tag_obj, 'componentHeadStyle') && isset($tag_obj->context->globalData)){
			$tag_obj->context->globalData->addComponentHeadStyle([$tag_obj, 'componentHeadStyle']);
		}

		// Set how many siblings this element has
		$tag_obj->props["nonRawSiblings"] = $nonRawSiblings;

		// Get the context this tag provides to its children
		$child_context = $tag_obj->getChildContext();
		$child_count = count($children_elements);

		if($children_elements){
			// Recursively build child tag objects; each child knows the sibling count
			$children = [];
			foreach($children_elements as $child_el){
				$children[] = $this->_buildTag($child_el, $child_context, $child_count);
			}
			$tag_obj->props["children"] = $children;
		}else{
			// Leaf node: capture raw inner HTML content (e.g. for mj-text)
			$content = (string)$element;
			$content = preg_replace('/^<[^>]+>/s', '', $content);
			$content = preg_replace('/<[^>]+>$/s', '', $content);
			// Normalize whitespace in text nodes (outside HTML tags) to match Node.js output
			// Skip normalization for mj-raw which must preserve content exactly
			if($tag_name !== 'mj-raw'){
				$content = preg_replace_callback(
					'/(<[^>]*>)|([^<]+)/',
					function($m){
						if(isset($m[1]) && strlen($m[1]) > 0){
							return $m[1];
						}
						return preg_replace('/\s+/', ' ', $m[2]);
					},
					$content
				);
				$content = trim($content);
			}
			$tag_obj->content = $content;
			$tag_obj->props["children"] = [];
		}

		return $tag_obj;
	}
}
