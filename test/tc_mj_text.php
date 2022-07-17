<?php
use Yarri\Mjml\Tags\MjText;

class TcMjText extends TcBase {

	function test(){
		$mj_text = new Yarri\Mjml\Tags\MjText(["content" => "Hello World!"]);

		$this->assertEquals([
			'align' => 'left',
			'color' => '#000000',
			'font-family' => 'Ubuntu, Helvetica, Arial, sans-serif',
			'font-size' => '13px',
			'line-height' => '1',
			'padding' => '10px 25px'
		],$mj_text->defaultAttributes);

		$this->assertEquals('left',$mj_text->getAttribute('align'));
		$this->assertEquals(null,$mj_text->getAttribute('non-existing'));
	}

	function test_render(){
		$mj_text = new Yarri\Mjml\Tags\MjText(["content" => "Hello World!"]);
		$this->assertEquals('<div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;">Hello World!</div>',$mj_text->renderContent());
		$this->assertEquals('<div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;">Hello World!</div>',$mj_text->render());

		$mj_text = new Yarri\Mjml\Tags\MjText(["content" => "Hello Universe!","attributes" => ["font-weight" => "bold"]]);
		$this->assertEquals('<div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:bold;line-height:1;text-align:left;color:#000000;">Hello Universe!</div>',$mj_text->renderContent());
		$this->assertEquals('<div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:bold;line-height:1;text-align:left;color:#000000;">Hello Universe!</div>',$mj_text->render());

		// render with height
		$mj_text = new Yarri\Mjml\Tags\MjText(["content" => "Nice day!", "attributes" => ["height" => "50"]]);
		$this->assertEquals('<div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;height:50;">Nice day!</div>',$mj_text->renderContent());
		$this->assertHtmlEquals('
			<table role="presentation" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td height="50" style="vertical-align:top;height:50;">
						<div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;height:50;">Nice day!</div>
					</td>
				</tr>
			</table>
		',$mj_text->render());
	}

	function test_styles(){
		$mj_text = new Yarri\Mjml\Tags\MjText();
		$this->assertEquals("font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;",$mj_text->styles("text"));

		$mj_text = new Yarri\Mjml\Tags\MjText(["attributes" => ["font-weight" => "bold"]]);
		$this->assertEquals("font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:bold;line-height:1;text-align:left;color:#000000;",$mj_text->styles("text"));

		$this->assertEquals("font-weight:bold;",$mj_text->styles(["font-weight" => "bold"]));
	}
}
