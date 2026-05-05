<?php
class TcMjml extends TcBase {

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

		$html = Yarri\Mjml::Mjml2Html($src);
		$html_node = $this->_mjml_node($src);

		$this->assertHtmlEquals($html_node,$html);
	}


	function test_full_document(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text>Hello!</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);

		// full HTML document structure
		$this->assertStringContains('<!doctype html>', $html);
		$this->assertStringContains('<html xmlns="http://www.w3.org/1999/xhtml"', $html);
		$this->assertStringContains('</html>', $html);
		$this->assertStringContains('<head>', $html);
		$this->assertStringContains('<body ', $html);
		$this->assertStringContains('word-spacing:normal;', $html);

		// media query for single column (100%)
		$this->assertStringContains('@media only screen and (min-width:480px)', $html);
		$this->assertStringContains('.mj-column-per-100 { width:100% !important; max-width: 100%; }', $html);
		$this->assertStringContains('.moz-text-html .mj-column-per-100', $html);
	}

	function test_two_columns_media_queries(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column><mj-text>A</mj-text></mj-column>
						<mj-column><mj-text>B</mj-text></mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);

		// each column is 50%
		$this->assertStringContains('.mj-column-per-50 { width:50% !important; max-width: 50%; }', $html);
	}

	function test_background_color(){
		$src = '
			<mjml>
				<mj-body background-color="#fafafa">
					<mj-section>
						<mj-column><mj-text>Hi</mj-text></mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('background-color:#fafafa;', $html);
	}

	function test_error(){
		$src = '<mjml><mj-bodi></wjwl>';
		$exception_msg = "";
		try {
			$html = Yarri\Mjml::Mjml2Html($src);
		}catch(Exception $e){
			$exception_msg = $e->getMessage();
		}
		$this->assertStringContains("Malformed MJML. XML parser error (76): Mismatched tag on line 1",$exception_msg);

		$src = '<mjml></mjml>';
		$exception_msg = "";
		try {
			$html = Yarri\Mjml::Mjml2Html($src);
		}catch(Exception $e){
			$exception_msg = $e->getMessage();
		}
		$this->assertStringContains("Malformed MJML. Element /mjml/mj-body not found.",$exception_msg);
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
}
