<?php
class TcMjAccordion extends TcBase {

	function test_integration_basic(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-accordion>
								<mj-accordion-element>
									<mj-accordion-title>FAQ Title</mj-accordion-title>
									<mj-accordion-text>FAQ Answer</mj-accordion-text>
								</mj-accordion-element>
							</mj-accordion>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('FAQ Title', $html);
		$this->assertStringContains('FAQ Answer', $html);
		$this->assertStringContains('mj-accordion', $html);
		$this->assertStringContains('mj-accordion-title', $html);
		$this->assertStringContains('mj-accordion-content', $html);
		$this->assertStringContains('mj-accordion-checkbox', $html);
		// headStyle CSS always injected
		$this->assertStringContains('noinput.mj-accordion-checkbox', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_icon_position_left(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-accordion icon-position="left">
								<mj-accordion-element>
									<mj-accordion-title>Left Icons</mj-accordion-title>
									<mj-accordion-text>Body text</mj-accordion-text>
								</mj-accordion-element>
							</mj-accordion>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Left Icons', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_custom_styling(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-accordion border="1px solid #ccc" font-family="Arial">
								<mj-accordion-element background-color="#f9f9f9">
									<mj-accordion-title color="#333" font-size="16px">Styled Title</mj-accordion-title>
									<mj-accordion-text color="#666" padding="20px">Styled content</mj-accordion-text>
								</mj-accordion-element>
							</mj-accordion>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('1px solid #ccc', $html);
		$this->assertStringContains('#333', $html);
		$this->assertStringContains('Styled content', $html);
	}
}
