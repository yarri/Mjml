<?php
class TcMjCarousel extends TcBase {

	function test_integration_basic(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-carousel>
								<mj-carousel-image src="https://example.com/img1.jpg" alt="Image 1" />
								<mj-carousel-image src="https://example.com/img2.jpg" alt="Image 2" />
								<mj-carousel-image src="https://example.com/img3.jpg" alt="Image 3" />
							</mj-carousel>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('img1.jpg', $html);
		$this->assertStringContains('img2.jpg', $html);
		$this->assertStringContains('img3.jpg', $html);
		$this->assertStringContains('mj-carousel-radio', $html);
		$this->assertStringContains('mj-carousel-image-1', $html);
		$this->assertStringContains('mj-carousel-image-2', $html);
		// Head CSS with dynamic carousel ID
		$this->assertStringContains('mj-carousel', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_with_href(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-carousel thumbnails="hidden">
								<mj-carousel-image src="https://example.com/a.jpg" href="https://example.com/page1" alt="A" />
								<mj-carousel-image src="https://example.com/b.jpg" href="https://example.com/page2" alt="B" />
							</mj-carousel>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('example.com/page1', $html);
		$this->assertStringContains('example.com/page2', $html);
		// Thumbnails hidden - no thumbnail anchor elements in HTML body
		$this->assertStringNotContains('class="mj-carousel-thumbnail', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}
}
