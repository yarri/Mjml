<?php
class TcMjml extends TcBase {

	function test_error(){
		$src = '<mjml><mj-bodi></wjwl>';
		$exception_msg = "";
		try {
			$html = Yarri\Mjml::Mjml2Html($src);
		}catch(Exception $e){
			$exception_msg = $e->getMessage();
		}
		$this->assertContains("Malformed MJML. XML parser error (76): Mismatched tag on line 1",$exception_msg);

		$src = '<mjml></mjml>';
		$exception_msg = "";
		try {
			$html = Yarri\Mjml::Mjml2Html($src);
		}catch(Exception $e){
			$exception_msg = $e->getMessage();
		}
		$this->assertContains("Malformed MJML. Element /mjml/mj-body not found.",$exception_msg);
	}

	function test(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text>
								Hello World!
								<a href="http://www.link.cz/">Link</a>
								Hello Boys!
							</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';

		echo $this->_mjml_node($src),"\n\n";

		echo Yarri\Mjml::Mjml2Html($src);

		exit;
	}

	function test_node(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text>
								Hello World!
								<a href="http://www.link.cz/">Link</a>
							</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';

		$tmpfile = Files::WriteToTemp($src);

		$cmd = "./node_modules/mjml/bin/mjml $tmpfile";
		$output = null;
		$retval = null;
		exec($cmd,$output,$retval);
		$output = join("\n",$output);

		Files::Unlink($tmpfile);

		$this->assertEquals(0,$retval);
		$this->assertTrue(strlen($output)>0);
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
