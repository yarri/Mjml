<?php
use Yarri\Mjml\Tags\MjImage;

class TcMjImage extends TcBase {

	function test_defaults(){
		$img = new MjImage();
		$this->assertEquals('center', $img->getAttribute('align'));
		$this->assertEquals('0', $img->getAttribute('border'));
		$this->assertEquals('auto', $img->getAttribute('height'));
		$this->assertEquals('_blank', $img->getAttribute('target'));
	}

	function test_render_basic(){
		$img = new MjImage(['attributes' => ['src' => '/img/photo.jpg', 'alt' => 'Photo']]);
		$html = $img->render();
		$this->assertStringContains('<img', $html);
		$this->assertStringContains('src="/img/photo.jpg"', $html);
		$this->assertStringContains('alt="Photo"', $html);
		// no link when href not set
		$this->assertStringNotContains('<a ', $html);
	}

	function test_render_with_href(){
		$img = new MjImage(['attributes' => [
			'src' => '/img/photo.jpg',
			'href' => 'https://example.com',
			'target' => '_blank',
		]]);
		$html = $img->render();
		$this->assertStringContains('<a ', $html);
		$this->assertStringContains('href="https://example.com"', $html);
		$this->assertStringContains('target="_blank"', $html);
	}

	function test_content_width(){
		// container is 600px, no explicit width → uses full box width
		$img = new MjImage(['attributes' => ['src' => '/img/photo.jpg']]);
		$img->context->containerWidth = '600px';
		$this->assertEquals(550, $img->getContentWidth()); // 600 - 2*25px padding

		// explicit width smaller than box
		$img2 = new MjImage(['attributes' => ['src' => '/img/photo.jpg', 'width' => '200px']]);
		$img2->context->containerWidth = '600px';
		$this->assertEquals(200, $img2->getContentWidth());
	}

	function test_integration(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-image src="/img/photo.jpg" alt="Test" href="https://example.com" />
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('src="/img/photo.jpg"', $html);
		$this->assertStringContains('alt="Test"', $html);
		$this->assertStringContains('href="https://example.com"', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
