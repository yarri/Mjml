<?php
use Yarri\Mjml\Tags\MjDivider;

class TcMjDivider extends TcBase {

	function test_defaults(){
		$d = new MjDivider();
		$this->assertEquals('#000000', $d->getAttribute('border-color'));
		$this->assertEquals('solid', $d->getAttribute('border-style'));
		$this->assertEquals('4px', $d->getAttribute('border-width'));
		$this->assertEquals('100%', $d->getAttribute('width'));
		$this->assertEquals('center', $d->getAttribute('align'));
	}

	function test_styles(){
		$d = new MjDivider();
		$styles = $d->getStyles();
		$this->assertStringContains('solid 4px #000000', $styles['p']['border-top']);
		$this->assertEquals('0px auto', $styles['p']['margin']);

		$d_left = new MjDivider(['attributes' => ['align' => 'left']]);
		$this->assertEquals('0px', $d_left->getStyles()['p']['margin']);

		$d_right = new MjDivider(['attributes' => ['align' => 'right']]);
		$this->assertEquals('0px 0px 0px auto', $d_right->getStyles()['p']['margin']);
	}

	function test_render(){
		$d = new MjDivider();
		$html = $d->render();
		$this->assertStringContains('<p ', $html);
		$this->assertStringContains('border-top:', $html);
		$this->assertStringContains('<!--[if mso | IE]>', $html);
	}

	function test_integration(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-divider border-color="#cccccc" border-width="1px" />
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('#cccccc', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
