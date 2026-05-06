<?php
namespace Yarri\Mjml;

class Utils {

	
	/**
	 * A better "alias" for htmlspecialchars()
	 *
	 * Taken from the ATK14 Framework
	 */
	static function h($string, $flags = null, $encoding = null){
		if(!is_string($string)){
			$string = (string)$string;
		}
		if(!isset($flags)){
			$flags =  ENT_COMPAT | ENT_QUOTES;
			if(defined("ENT_HTML401")){ $flags = $flags | ENT_HTML401; }
		}
		if(!isset($encoding)){
			// as of PHP5.4 the default encoding is UTF-8, it causes troubles in non UTF-8 applications,
			// I think that the encoding ISO-8859-1 works well in UTF-8 applications
			$encoding = "ISO-8859-1";
		}
		return htmlspecialchars((string)$string,$flags,$encoding);
	}
}
