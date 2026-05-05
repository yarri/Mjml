<?php
class TcMjColumn extends TcBase {

	function test_basic(){
		$src = '
			<mjml>
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
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_background_color(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column background-color="#ff0000">
							<mj-text>Colored column</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('#ff0000', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_padding_gutter(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column padding="20px">
							<mj-text>Padded column</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('padding:20px', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_border(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column border="1px solid #cccccc" border-radius="4px">
							<mj-text>Bordered column</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('1px solid #cccccc', $html);
		$this->assertStringContains('border-radius:4px', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_two_columns_explicit_width(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column width="33%">
							<mj-text>One third</mj-text>
						</mj-column>
						<mj-column width="67%">
							<mj-text>Two thirds</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('mj-column-per-33', $html);
		$this->assertStringContains('mj-column-per-67', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_inner_border(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column padding="10px" inner-border="2px solid #000000" inner-border-radius="8px">
							<mj-text>Inner bordered column</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('2px solid #000000', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_vertical_align(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column vertical-align="middle">
							<mj-text>Middle aligned</mj-text>
						</mj-column>
						<mj-column vertical-align="bottom">
							<mj-text>Bottom aligned</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('vertical-align:middle', $html);
		$this->assertStringContains('vertical-align:bottom', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
