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

	function test_realistic_newsletter(){
		$src = '
			<mjml>
				<mj-head>
					<mj-title>Monthly Newsletter</mj-title>
					<mj-preview>Check out our latest news!</mj-preview>
					<mj-attributes>
						<mj-text font-family="Arial, sans-serif" font-size="14px" color="#333333" />
						<mj-button background-color="#0066cc" color="#ffffff" />
					</mj-attributes>
					<mj-style>.footer-link { color: #999999; }</mj-style>
				</mj-head>
				<mj-body background-color="#f4f4f4">
					<mj-section background-color="#0066cc" padding="20px">
						<mj-column>
							<mj-image src="https://example.com/logo.png" alt="Logo" width="120px" />
							<mj-text color="#ffffff" font-size="24px" font-weight="bold" align="center">
								Monthly Newsletter
							</mj-text>
						</mj-column>
					</mj-section>
					<mj-section background-color="#ffffff" padding="30px 20px">
						<mj-column>
							<mj-text font-size="18px" font-weight="bold">Welcome back!</mj-text>
							<mj-text>Here is what happened this month. We have exciting news to share with you.</mj-text>
							<mj-button href="https://example.com/read-more">Read More</mj-button>
						</mj-column>
					</mj-section>
					<mj-section background-color="#ffffff" padding="0 20px 20px">
						<mj-column width="50%" padding="10px">
							<mj-image src="https://example.com/article1.jpg" alt="Article 1" />
							<mj-text font-weight="bold">Article One</mj-text>
							<mj-text>Short description of the first article goes here.</mj-text>
						</mj-column>
						<mj-column width="50%" padding="10px">
							<mj-image src="https://example.com/article2.jpg" alt="Article 2" />
							<mj-text font-weight="bold">Article Two</mj-text>
							<mj-text>Short description of the second article goes here.</mj-text>
						</mj-column>
					</mj-section>
					<mj-section background-color="#ffffff" padding="10px 20px 30px">
						<mj-column>
							<mj-divider border-color="#eeeeee" border-width="1px" />
							<mj-social font-size="12px" icon-size="20px" mode="horizontal">
								<mj-social-element name="facebook" href="https://facebook.com/example">Facebook</mj-social-element>
								<mj-social-element name="twitter" href="https://twitter.com/example">Twitter</mj-social-element>
							</mj-social>
						</mj-column>
					</mj-section>
					<mj-section padding="20px">
						<mj-column>
							<mj-text align="center" color="#999999" font-size="12px">
								&#169; 2025 Example Company. All rights reserved.
							</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Monthly Newsletter', $html);
		$this->assertStringContains('Check out our latest news!', $html);
		$this->assertStringContains('Read More', $html);
		$this->assertStringContains('Article One', $html);
		$this->assertStringContains('Article Two', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
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

		$cmd = __DIR__ . "/../node_modules/mjml/bin/mjml $tmpfile";
		$output = null;
		$retval = null;
		exec($cmd,$output,$retval);
		$output = join("\n",$output);

		Files::Unlink($tmpfile);

		$this->assertEquals(0,$retval);
		$this->assertTrue(strlen($output)>0);
	}
}
