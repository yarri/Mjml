<?php
namespace Yarri;

class Mjml {

	static function Mjml2Html($mjml,$options = []){
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
