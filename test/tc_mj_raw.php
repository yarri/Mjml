<?php
class TcMjRaw extends TcBase {

	function test_render(){
		$raw = new Yarri\Mjml\Tags\MjRaw(['content' => '<p>Hello raw!</p>']);
		$this->assertEquals('<p>Hello raw!</p>', $raw->render());
	}

	function test_preserves_content_exactly(){
		$content = '<div class="custom">  spaced   content  </div>';
		$raw = new Yarri\Mjml\Tags\MjRaw(['content' => $content]);
		$this->assertEquals($content, $raw->render());
	}

	function test_integration(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-raw><p class="custom">Raw HTML</p></mj-raw>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('class="custom"', $html);
		$this->assertStringContains('Raw HTML', $html);
	}
}
