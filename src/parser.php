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
		$body_tag = $this->_buildTag($this->body);
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

		return $out;
	}

	/**
	 * Recursively build a tag object tree with proper context propagation.
	 *
	 * @param \XMole $element
	 * @param object|null $context  Context object (with containerWidth) from parent
	 * @param int $nonRawSiblings   How many mj-* siblings this element has (including itself)
	 * @return \Yarri\Mjml\Tags\_Tag
	 */
	function _buildTag(\XMole $element, $context = null, $nonRawSiblings = 1){
		$tag_name = $element->get_root_name();
		$attributes = $element->get_root_attributes();

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
			$content = preg_replace_callback(
				'/(<[^>]*>)|([^<]+)/',
				function($m){
					if(isset($m[1]) && strlen($m[1]) > 0){
						return $m[1];
					}
					// Normalize whitespace in text nodes
					return preg_replace('/\s+/', ' ', $m[2]);
				},
				$content
			);
			$content = trim($content);
			$tag_obj->content = $content;
			$tag_obj->props["children"] = [];
		}

		return $tag_obj;
	}
}
