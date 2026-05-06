<?php
class TcIssueMjClass extends TcBase {

	function test_basic(){
		$src = '
			<mjml>
				<mj-head>
					<mj-attributes>
						<mj-class name="headline" font-size="24px" color="#cc0000" font-weight="bold" />
					</mj-attributes>
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text mj-class="headline">Hello World</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('font-size:24px', $html);
		$this->assertStringContains('color:#cc0000', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_multiple_classes(){
		$src = '
			<mjml>
				<mj-head>
					<mj-attributes>
						<mj-class name="big" font-size="20px" />
						<mj-class name="red" color="#ff0000" />
					</mj-attributes>
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text mj-class="big red">Styled text</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('font-size:20px', $html);
		$this->assertStringContains('color:#ff0000', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_explicit_attr_overrides_class(){
		$src = '
			<mjml>
				<mj-head>
					<mj-attributes>
						<mj-class name="blue" color="#0000ff" />
					</mj-attributes>
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text mj-class="blue" color="#00ff00">Overridden</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		// explicit color wins over mj-class color
		$this->assertStringContains('color:#00ff00', $html);
		$this->assertStringNotContains('color:#0000ff', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_class_combined_with_mj_all_and_type_default(){
		$src = '
			<mjml>
				<mj-head>
					<mj-attributes>
						<mj-all font-family="Arial, sans-serif" />
						<mj-text color="#333333" />
						<mj-class name="hero" font-size="28px" color="#0066cc" />
					</mj-attributes>
				</mj-head>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text mj-class="hero">Hero text</mj-text>
							<mj-text>Normal text</mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('font-size:28px', $html);
		$this->assertStringContains('#0066cc', $html);
		$this->assertStringContains('#333333', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
