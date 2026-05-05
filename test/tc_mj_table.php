<?php
use Yarri\Mjml\Tags\MjTable;

class TcMjTable extends TcBase {

	function test_defaults(){
		$t = new MjTable();
		$this->assertEquals('left', $t->getAttribute('align'));
		$this->assertEquals('#000000', $t->getAttribute('color'));
		$this->assertEquals('13px', $t->getAttribute('font-size'));
		$this->assertEquals('auto', $t->getAttribute('table-layout'));
		$this->assertEquals('100%', $t->getAttribute('width'));
	}

	function test_render(){
		$t = new MjTable(['content' => '<tr><td>Cell</td></tr>']);
		$html = $t->render();
		$this->assertStringContains('<table ', $html);
		$this->assertStringContains('Cell', $html);
		$this->assertStringContains('color:#000000;', $html);
	}

	function test_integration(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-table>
								<tr><th>Name</th><th>Score</th></tr>
								<tr><td>Alice</td><td>100</td></tr>
							</mj-table>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('Alice', $html);
		$this->assertStringContains('Score', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
