<?php
class TcMjSection extends TcBase {

	function test_componentName(){
		$mj_section = new Yarri\Mjml\Tags\MjSection();
		$this->assertEquals("mj-section",$mj_section::$componentName);
	}

	function test_background(){
		$mj_section = new Yarri\Mjml\Tags\MjSection();
		$this->assertEquals(["x" => "center", "y" => "top"],$mj_section->parseBackgroundPosition());
		$this->assertEquals(["posX" => "center", "posY" => "top"],$mj_section->getBackgroundPosition());
		$this->assertEquals("center top",$mj_section->getBackgroundString());

		$mj_section = new Yarri\Mjml\Tags\MjSection(["attributes" => ["background-position" => "bottom"]]);
		$this->assertEquals(["x" => "center", "y" => "bottom"],$mj_section->parseBackgroundPosition());
		$this->assertEquals(["posX" => "center", "posY" => "bottom"],$mj_section->getBackgroundPosition());
		$this->assertEquals("center bottom",$mj_section->getBackgroundString());

		$mj_section = new Yarri\Mjml\Tags\MjSection(["attributes" => ["background-position" => "center right"]]);
		$this->assertEquals(["x" => "right", "y" => "center"],$mj_section->parseBackgroundPosition());
		$this->assertEquals(["posX" => "right", "posY" => "center"],$mj_section->getBackgroundPosition());
		$this->assertEquals("right center",$mj_section->getBackgroundString());
	}

	function test_hasBackground(){
		$mj_section = new Yarri\Mjml\Tags\MjSection();
		$this->assertFalse($mj_section->hasBackground());

		$mj_section = new Yarri\Mjml\Tags\MjSection(["attributes" => ["background-url" => "/public/rose.jpg"]]);
		$this->assertTrue($mj_section->hasBackground());
	}

	function test_integration_background_color(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section background-color="#ffeeff">
						<mj-column><mj-text>Hello</mj-text></mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('#ffeeff', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_integration_background_url(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section background-url="https://example.com/bg.jpg" background-size="cover" background-repeat="no-repeat">
						<mj-column><mj-text>Hello</mj-text></mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('example.com/bg.jpg', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_integration_full_width(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section full-width="full-width" background-color="#dddddd">
						<mj-column><mj-text>Full width section</mj-text></mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Full width section', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_integration_border_padding(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section border="1px solid #aaaaaa" padding="40px 20px" border-radius="8px">
						<mj-column><mj-text>Bordered section</mj-text></mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('1px solid #aaaaaa', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
