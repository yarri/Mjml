<?php
class TcMjmlHead extends TcBase {

	function test_title(){
		$src = '
			<mjml>
				<mj-head>
					<mj-title>My Newsletter</mj-title>
				</mj-head>
				<mj-body></mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('My Newsletter', $html);
		// must appear inside <title> tag
		$this->assertTrue((bool)preg_match('/<title>[^<]*My Newsletter[^<]*<\/title>/s', $html));
	}

	function test_preview(){
		$src = '
			<mjml>
				<mj-head>
					<mj-preview>Check this out!</mj-preview>
				</mj-head>
				<mj-body></mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Check this out!', $html);
		$this->assertStringContains('display:none', $html);
		$this->assertStringContains('overflow:hidden', $html);
	}

	function test_breakpoint(){
		$src = '
			<mjml>
				<mj-head>
					<mj-breakpoint width="600px" />
				</mj-head>
				<mj-body>
					<mj-section><mj-column><mj-text>Hi</mj-text></mj-column></mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('min-width:600px', $html);
		$this->assertStringNotContains('min-width:480px', $html);
	}

	function test_style(){
		$src = '
			<mjml>
				<mj-head>
					<mj-style>.custom { color: red; }</mj-style>
				</mj-head>
				<mj-body></mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('.custom { color: red; }', $html);
	}

	function test_font(){
		$src = '
			<mjml>
				<mj-head>
					<mj-font name="Roboto" href="https://fonts.googleapis.com/css?family=Roboto" />
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text font-family="Roboto, Arial">Hello</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('fonts.googleapis.com/css?family=Roboto', $html);
		$this->assertStringContains('@import url(', $html);
	}

	function test_font_not_included_when_unused(){
		$src = '
			<mjml>
				<mj-head>
					<mj-font name="Roboto" href="https://fonts.googleapis.com/css?family=Roboto" />
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text>Hello</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		// font not used in font-family → should not be imported
		$this->assertStringNotContains('fonts.googleapis.com/css?family=Roboto', $html);
	}

	function test_attributes_global_default(){
		$src = '
			<mjml>
				<mj-head>
					<mj-attributes>
						<mj-text color="#333333" />
					</mj-attributes>
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text>Hello</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('color:#333333;', $html);
	}

	function test_attributes_explicit_overrides_default(){
		$src = '
			<mjml>
				<mj-head>
					<mj-attributes>
						<mj-text color="#333333" />
					</mj-attributes>
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text color="#ff0000">Hello</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('color:#ff0000;', $html);
		$this->assertStringNotContains('color:#333333;', $html);
	}

	function test_html_attributes(){
		$src = '
			<mjml>
				<mj-head>
					<mj-html-attributes>
						<mj-selector path=".custom-section">
							<mj-html-attribute name="data-tracking">newsletter</mj-html-attribute>
						</mj-selector>
					</mj-html-attributes>
				</mj-head>
				<mj-body>
					<mj-section css-class="custom-section">
						<mj-column><mj-text>Hello</mj-text></mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('data-tracking="newsletter"', $html);
	}

	function test_attributes_mj_all(){
		$src = '
			<mjml>
				<mj-head>
					<mj-attributes>
						<mj-all font-family="Georgia, serif" />
					</mj-attributes>
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text>Hello</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Georgia, serif', $html);
	}
}
