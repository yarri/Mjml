<?php
class TcMjWrapper extends TcBase {

	function test_integration(){
		$src = '
			<mjml>
				<mj-body>
					<mj-wrapper background-color="#f4f4f4">
						<mj-section>
							<mj-column>
								<mj-text>Inside wrapper</mj-text>
							</mj-column>
						</mj-section>
					</mj-wrapper>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Inside wrapper', $html);
		$this->assertStringContains('#f4f4f4', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_multiple_sections(){
		$src = '
			<mjml>
				<mj-body>
					<mj-wrapper>
						<mj-section>
							<mj-column><mj-text>Section 1</mj-text></mj-column>
						</mj-section>
						<mj-section>
							<mj-column><mj-text>Section 2</mj-text></mj-column>
						</mj-section>
					</mj-wrapper>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Section 1', $html);
		$this->assertStringContains('Section 2', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
