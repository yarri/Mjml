<?php
use Yarri\Mjml\Tags\MjSpacer;

class TcMjSpacer extends TcBase {

	function test_defaults(){
		$s = new MjSpacer();
		$this->assertEquals('20px', $s->getAttribute('height'));
	}

	function test_render(){
		$s = new MjSpacer();
		$html = $s->render();
		$this->assertStringContains('<div', $html);
		$this->assertStringContains('height:20px;', $html);
		$this->assertStringContains('line-height:20px;', $html);
		$this->assertStringContains('&#8202;', $html);
	}

	function test_custom_height(){
		$s = new MjSpacer(['attributes' => ['height' => '40px']]);
		$html = $s->render();
		$this->assertStringContains('height:40px;', $html);
		$this->assertStringContains('line-height:40px;', $html);
	}

	function test_integration(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-spacer height="30px" />
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('height:30px;', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
