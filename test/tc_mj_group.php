<?php
class TcMjGroup extends TcBase {

	function test_integration_basic(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-group>
							<mj-column><mj-text>Col A</mj-text></mj-column>
							<mj-column><mj-text>Col B</mj-text></mj-column>
						</mj-group>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Col A', $html);
		$this->assertStringContains('Col B', $html);
		$this->assertStringContains('mj-outlook-group-fix', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_group_with_column_mixed(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column width="33%"><mj-text>Left</mj-text></mj-column>
						<mj-group width="67%">
							<mj-column><mj-text>Middle</mj-text></mj-column>
							<mj-column><mj-text>Right</mj-text></mj-column>
						</mj-group>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Left', $html);
		$this->assertStringContains('Middle', $html);
		$this->assertStringContains('Right', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
