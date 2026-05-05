<?php
class TcBase extends TcSuperbase {

	function _compare_html($expected,$actual){
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
