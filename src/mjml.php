<?php
namespace Yarri;

class Mjml {

	static function Mjml2Html($mjml,$options = []){
		$replaces_for_malformations = [];
		$i = 1;
		$uniqid = uniqid();
		$mjml = preg_replace_callback(
			'/(<(mj-text|mj-button|mj-accordion-title|mj-accordion-text|mj-table|mj-raw|mj-social-element|mj-navbar-link)\b(?:[^>"\'\/]|"[^"]*"|\'[^\']*\'|\/(?!>))*>)(.*?)(<\/\2>)/s',
			function($matches) use(&$i,$uniqid,&$replaces_for_malformations){
				$content = $matches[3];

				$xmole = new \XMole("<xml>$content</xml>");
				if(!$xmole->error()){
					// Not malformed
					return $matches[0];
				}

				$replacement_key = "malformed-content-$i-$uniqid";
				$i++;

				// Count of lines
				$lines_count = count(explode("\n",$content));

				// To preserve the content line height
				$replacement_key .= str_repeat("\n.",$lines_count-1);
				$replacement_key_alt = str_replace("\n"," ",$replacement_key); // Some MJML tags "normalize" the innert text (i.e. "\n" -> " ")

				$replaces_for_malformations[$replacement_key] = $content;
				$replaces_for_malformations[$replacement_key_alt] = $content;
				return "$matches[1]$replacement_key$matches[4]";
			},
			$mjml
		);

		$xmole = new \XMole($mjml,["trim_data" => false]);
		if($xmole->error()){
			throw new \Exception("Malformed MJML. ".$xmole->get_error_message());
		}

		$body = $xmole->get_xmole("/mjml/mj-body");
		if(!$body){
			throw new \Exception("Malformed MJML. Element /mjml/mj-body not found.");
		}

		$head = $xmole->get_xmole("/mjml/mj-head");
		if(!$head){
			$head = new \Xmole("<mj-head></mj-head>");
		}

		$parser = new Mjml\Parser($head,$body);
		$html = $parser->parse();

		if($replaces_for_malformations){
			$html = strtr($html,$replaces_for_malformations);
		}

		return $html;
	}

	static function _dump_children($xmole){
	 	while($child = $xmole->get_next_child()){
			echo "\n!!!----\n";
			echo $child->get_root_name(),"\n";
			var_dump($child->get_root_attributes());
			echo $child;
			self::_dump_children($child);
		}
	}
}
