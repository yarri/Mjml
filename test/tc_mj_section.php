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
}
