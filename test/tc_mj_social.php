<?php
class TcMjSocial extends TcBase {

	function test_integration_horizontal(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-social font-size="12px" mode="horizontal">
								<mj-social-element name="facebook" href="https://facebook.com">Facebook</mj-social-element>
								<mj-social-element name="twitter" href="https://twitter.com">Twitter</mj-social-element>
							</mj-social>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Facebook', $html);
		$this->assertStringContains('Twitter', $html);
		$this->assertStringContains('facebook.png', $html);
		// Share URL generated from href
		$this->assertStringContains('facebook.com/sharer', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_integration_vertical(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-social mode="vertical">
								<mj-social-element name="linkedin" href="https://linkedin.com">LinkedIn</mj-social-element>
							</mj-social>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('LinkedIn', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_custom_icon(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-social>
								<mj-social-element
									name="custom"
									src="https://example.com/icon.png"
									href="https://example.com"
									background-color="#aabbcc"
								>Visit us</mj-social-element>
							</mj-social>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('example.com/icon.png', $html);
		$this->assertStringContains('#aabbcc', $html);
	}
}
