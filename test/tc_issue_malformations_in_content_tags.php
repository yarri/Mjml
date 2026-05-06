<?php
class TcIssueMalformationsInContentTags extends TcBase {

	function test(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-text color="deep-purple">Hello Boys!<br> <span><a>WHAT?</span></a></mj-text>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);

		$this->assertStringContains("color:deep-purple",$html);

		// Malformations in mj-text must survive
		$this->assertStringContains("Hello Boys!<br>",$html);
		$this->assertStringContains("<span><a>WHAT?</span></a>",$html);
	}

	function test_other_tags(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-button href="http://example.com">Click <br> here</mj-button>
							<mj-table><tr><td>Cell <br> content</td></tr></mj-table>
							<mj-accordion>
								<mj-accordion-element>
									<mj-accordion-title>Title <br> text</mj-accordion-title>
									<mj-accordion-text>Body <br> text</mj-accordion-text>
								</mj-accordion-element>
							</mj-accordion>
							<mj-social>
								<mj-social-element name="facebook" href="http://example.com">Like <br> us</mj-social-element>
							</mj-social>
							<mj-navbar>
								<mj-navbar-link href="http://example.com">Home <br> page</mj-navbar-link>
							</mj-navbar>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);

		$this->assertStringContains("Click <br> here", $html);
		$this->assertStringContains("Cell <br> content", $html);
		$this->assertStringContains("Title <br> text", $html);
		$this->assertStringContains("Body <br> text", $html);
		$this->assertStringContains("Like <br> us", $html);
		$this->assertStringContains("Home <br> page", $html);
	}
}
