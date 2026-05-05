<?php
class TcMjHero extends TcBase {

	function test_integration_fixed_height(){
		$src = '
			<mjml>
				<mj-body>
					<mj-hero
						background-url="https://example.com/bg.jpg"
						background-color="#1e90ff"
						height="400px"
					>
						<mj-text color="#ffffff">Hero text</mj-text>
					</mj-hero>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Hero text', $html);
		$this->assertStringContains('bg.jpg', $html);
		$this->assertStringContains('#1e90ff', $html);
		// Outlook VML image
		$this->assertStringContains('<v:image', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_integration_no_background(){
		$src = '
			<mjml>
				<mj-body>
					<mj-hero height="200px" background-color="#ffcc00">
						<mj-text>Simple hero</mj-text>
					</mj-hero>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Simple hero', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
