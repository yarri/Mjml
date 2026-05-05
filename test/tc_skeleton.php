<?php
use Yarri\Mjml\Skeleton;

class TcSkeleton extends TcBase {

	function test_buildMediaQueriesTags(){
		// empty – returns empty string
		$this->assertEquals('', Skeleton::buildMediaQueriesTags('480px', []));

		$out = Skeleton::buildMediaQueriesTags('480px', [
			'mj-column-per-100' => '{ width:100% !important; max-width: 100%; }',
		]);
		$this->assertStringContains('@media only screen and (min-width:480px)', $out);
		$this->assertStringContains('.mj-column-per-100 { width:100% !important; max-width: 100%; }', $out);
		// Thunderbird variant
		$this->assertStringContains('.moz-text-html .mj-column-per-100 { width:100% !important; max-width: 100%; }', $out);
		// custom breakpoint
		$out2 = Skeleton::buildMediaQueriesTags('600px', [
			'mj-column-per-50' => '{ width:50% !important; max-width: 50%; }',
		]);
		$this->assertStringContains('min-width:600px', $out2);
		$this->assertStringNotContains('min-width:480px', $out2);
	}

	function test_buildPreview(){
		$this->assertEquals('', Skeleton::buildPreview(''));
		$this->assertEquals('', Skeleton::buildPreview(null));

		$out = Skeleton::buildPreview('Check this out!');
		$this->assertStringContains('Check this out!', $out);
		$this->assertStringContains('display:none', $out);
		$this->assertStringContains('overflow:hidden', $out);
	}

	function test_render_structure(){
		$out = Skeleton::render(['content' => '<div>Hello</div>']);

		$this->assertStringContains('<!doctype html>', $out);
		$this->assertStringContains('xmlns="http://www.w3.org/1999/xhtml"', $out);
		$this->assertStringContains('xmlns:v="urn:schemas-microsoft-com:vml"', $out);
		$this->assertStringContains('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">', $out);
		$this->assertStringContains('<body style="word-spacing:normal;">', $out);
		$this->assertStringContains('<div>Hello</div>', $out);
		// no background-color when not set
		$this->assertStringNotContains('background-color', $out);
	}

	function test_render_background_color(){
		$out = Skeleton::render(['content' => '', 'backgroundColor' => '#ff0000']);
		$this->assertStringContains('word-spacing:normal;background-color:#ff0000;', $out);
	}

	function test_render_media_queries(){
		$out = Skeleton::render([
			'content' => '',
			'mediaQueries' => [
				'mj-column-per-100' => '{ width:100% !important; max-width: 100%; }',
			],
		]);
		$this->assertStringContains('@media only screen and (min-width:480px)', $out);
		$this->assertStringContains('.mj-column-per-100 { width:100% !important; max-width: 100%; }', $out);
	}

	function test_render_title(){
		$out = Skeleton::render(['content' => '', 'title' => 'My Newsletter']);
		$this->assertStringContains('<title>', $out);
		$this->assertStringContains('My Newsletter', $out);
	}

	function test_mergeOutlookConditionals(){
		$in = '<!--[if mso | IE]><tr><![endif]--><!--[if mso | IE]></tr><![endif]-->';
		$out = Skeleton::mergeOutlookConditionals($in);
		$this->assertStringNotContains('<![endif]--><!--[if mso | IE]>', $out);
		$this->assertStringContains('<tr>', $out);
		$this->assertStringContains('</tr>', $out);
	}

	function test_minifyOutlookConditionals(){
		$in = "<!--[if mso | IE]>\n  <table>\n\n  </table>\n<![endif]-->";
		$out = Skeleton::minifyOutlookConditionals($in);
		$this->assertStringContains('<table>', $out);
		// multiple spaces collapsed
		$this->assertTrue(!preg_match('/\s{3,}/', $out));
	}
}
