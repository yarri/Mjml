<?php
use Yarri\Mjml\Tags\MjButton;

class TcMjButton extends TcBase {

	function test_defaults(){
		$b = new MjButton();
		$this->assertEquals('#414141', $b->getAttribute('background-color'));
		$this->assertEquals('#ffffff', $b->getAttribute('color'));
		$this->assertEquals('3px', $b->getAttribute('border-radius'));
		$this->assertEquals('_blank', $b->getAttribute('target'));
		$this->assertEquals('none', $b->getAttribute('text-decoration'));
	}

	function test_render_with_href(){
		$b = new MjButton(['content' => 'Click me', 'attributes' => [
			'href' => 'https://example.com',
		]]);
		$html = $b->render();
		$this->assertStringContains('<a ', $html);
		$this->assertStringContains('href="https://example.com"', $html);
		$this->assertStringContains('Click me', $html);
	}

	function test_render_without_href(){
		$b = new MjButton(['content' => 'No link']);
		$html = $b->render();
		// renders <p> instead of <a> when no href
		$this->assertStringContains('<p ', $html);
		$this->assertStringNotContains('<a ', $html);
		$this->assertStringContains('No link', $html);
	}

	function test_styles_content(){
		$b = new MjButton(['attributes' => ['background-color' => '#ff0000', 'color' => '#ffffff']]);
		$styles = $b->getStyles();
		$this->assertEquals('#ff0000', $styles['content']['background']);
		$this->assertEquals('#ffffff', $styles['content']['color']);
		$this->assertEquals('none', $styles['content']['text-decoration']);
	}

	function test_integration(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-button href="https://example.com" background-color="#ff6600">
								Buy Now
							</mj-button>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('href="https://example.com"', $html);
		$this->assertStringContains('#ff6600', $html);
		$this->assertStringContains('Buy Now', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
