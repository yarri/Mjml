<?php
class TcBase extends TcSuperbase {

	function _compare_html($expected,$actual){
		// Strip DOCTYPE declaration (PHP expat cannot parse it)
		$expected = preg_replace('/<!doctype[^>]*>/si', '', $expected);
		$actual = preg_replace('/<!doctype[^>]*>/si', '', $actual);
		// Extract body content if full HTML document
		if(preg_match('/<body[^>]*>(.*?)<\/body>/si', $expected, $m)){
			$expected = $m[1];
		}
		if(preg_match('/<body[^>]*>(.*?)<\/body>/si', $actual, $m)){
			$actual = $m[1];
		}
		// Normalize bare & to &amp; so expat XML parser can handle HTML from mjml node.js
		// (Node.js MJML outputs unescaped & in href attributes like ?foo=1&bar=2)
		$expected = preg_replace('/&(?![a-zA-Z#][a-zA-Z0-9]*;)/', '&amp;', $expected);
		$actual = preg_replace('/&(?![a-zA-Z#][a-zA-Z0-9]*;)/', '&amp;', $actual);
		// Normalize carousel random IDs so PHP and Node.js outputs can be compared
		// PHP uses 12-char IDs (bin2hex(random_bytes(6))), Node.js uses 16-char IDs
		$expected = preg_replace('/carousel-(?:[a-z]+-)*[0-9a-f]{12,16}/', 'carousel-ID', $expected);
		$actual = preg_replace('/carousel-(?:[a-z]+-)*[0-9a-f]{12,16}/', 'carousel-ID', $actual);
		// Also normalize mj-menu-checkbox random keys in navbar
		$expected = preg_replace('/(?<=id=")[0-9a-f]{16}(?=")/', 'MENU-KEY', $expected);
		$actual = preg_replace('/(?<=id=")[0-9a-f]{16}(?=")/', 'MENU-KEY', $actual);
		$expected = preg_replace('/(?<=for=")[0-9a-f]{16}(?=")/', 'MENU-KEY', $expected);
		$actual = preg_replace('/(?<=for=")[0-9a-f]{16}(?=")/', 'MENU-KEY', $actual);
		$expected = new XMole("<xml>$expected</xml>");
		$actual = new XMole("<xml>$actual</xml>");
		return XMole::AreSame($expected,$actual);
	}

	function assertHtmlEquals($expected,$actual){
		$this->assertTrue($this->_compare_html($expected,$actual),"\n\n### expected ###\n$expected\n\n### actual ###\n$actual\n\n");
	}

	function _mjml_node($src){
		$tmpfile = Files::WriteToTemp($src);

		$cmd = __DIR__ . "/../node_modules/mjml/bin/mjml $tmpfile";
		$output = null;
		$retval = null;
		exec($cmd,$output,$retval);
		$output = join("\n",$output);

		Files::Unlink($tmpfile);

		$this->assertEquals(0,$retval);
		$this->assertTrue(strlen($output)>0);

		return $output;
	}
}
