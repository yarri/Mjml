<?php
use Yarri\Mjml\Tags\_Tag;

class TcTag extends TcBase {

	function test(){
		$tag = new _Tag();

		$this->assertEquals("port-outlook",$tag->suffixCssClasses("port","outlook"));
		$this->assertEquals("port-outlook full-width-outlook",$tag->suffixCssClasses("port full-width","outlook"));
		$this->assertEquals("",$tag->suffixCssClasses("","outlook"));
	}
}
