<?php
namespace Yarri\Mjml;

class Parser {

	protected $head;
	protected $body;

	function __construct(\XMole $head,\XMole $body){
		$this->head = $head;
		$this->body = $body;
	}

	function parse(){
		$out = $this->_parse($this->body);

		// removing "\n" and "\t" inside tags
		$out = preg_replace_callback(
			'/(<.*?>)/s',
			function($matches){
				$tag = $matches[1];
				$tag = preg_replace('/[ \t]*\n[ \t]*/',' ',$tag);
				$tag = preg_replace('/ >$/','>',$tag);
				return $tag;
			},
			$out
		);

		return $out;
	}

	function _parse(\XMole $element){
		$tag = $element->get_root_name(); // e.g. "mj-body", "mj-text"...
		$attributes = $element->get_root_attributes();
		$content = (string)$element;
		$content = preg_replace('/^<[^>]+>/s','',$content);
		$content = preg_replace('/<[^>]+>$/s','',$content);

		$children = $element->get_children();
		$children = array_filter($children, function($child){ return !!preg_match('/^mj-/',$child->get_root_name()); });
		$children = array_values($children);

		if($children){
			$content = [];
			foreach($children as $child){
				$content[] = $this->_parse($child);
			}
			$content = join("",$content);
		}

		$tag_class = \String4::ToObject($tag)->replace('-','_')->camelize()->toString(); // "mj-text" -> "MjText";
		$tag_class = "Yarri\\Mjml\\Tags\\$tag_class";

		$tag = new $tag_class([
			"content" => $content,
			"attributes" => $attributes,
		]);

		return $tag->render();
	}
}
