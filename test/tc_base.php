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
		$expected = new XMole("<xml>$expected</xml>");
		$actual = new XMole("<xml>$actual</xml>");
		return XMole::AreSame($expected,$actual);
	}

	function assertHtmlEquals($expected,$actual){
		$this->assertTrue($this->_compare_html($expected,$actual),"\n\n### expected ###\n$expected\n\n### actual ###\n$actual\n\n");
	}

	function _mjml_node($src){
		$tmpfile = Files::WriteToTemp($src);

		$cmd = "./node_modules/mjml/bin/mjml $tmpfile";
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
