<?php
use Yarri\Mjml\GlobalData;

class TcGlobalData extends TcBase {

	function test_defaults(){
		$gd = new GlobalData();
		$this->assertEquals([], $gd->mediaQueries);
		$this->assertEquals('', $gd->backgroundColor);
		$this->assertEquals('480px', $gd->breakpoint);
		$this->assertEquals('', $gd->title);
		$this->assertEquals('', $gd->preview);
	}

	function test_addMediaQuery(){
		$gd = new GlobalData();
		$gd->addMediaQuery('mj-column-per-100', 100, '%');
		$this->assertEquals([
			'mj-column-per-100' => '{ width:100% !important; max-width: 100%; }'
		], $gd->mediaQueries);

		$gd->addMediaQuery('mj-column-px-200', 200, 'px');
		$this->assertEquals('{ width:200px !important; max-width: 200px; }', $gd->mediaQueries['mj-column-px-200']);

		// same class registered twice – second wins
		$gd->addMediaQuery('mj-column-per-100', 100, '%');
		$this->assertContains('mj-column-per-100', array_keys($gd->mediaQueries));
		$this->assertEquals(2, count($gd->mediaQueries));
	}

	function test_setBackgroundColor(){
		$gd = new GlobalData();
		$this->assertEquals('', $gd->backgroundColor);

		$gd->setBackgroundColor('#ff0000');
		$this->assertEquals('#ff0000', $gd->backgroundColor);

		// empty string should NOT overwrite existing value
		$gd->setBackgroundColor('');
		$this->assertEquals('#ff0000', $gd->backgroundColor);

		// null should NOT overwrite existing value
		$gd->setBackgroundColor(null);
		$this->assertEquals('#ff0000', $gd->backgroundColor);
	}
}
