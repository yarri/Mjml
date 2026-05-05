<?php
class TcMjNavbar extends TcBase {

	function test_integration_basic(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-navbar>
								<mj-navbar-link href="/about">About</mj-navbar-link>
								<mj-navbar-link href="/contact">Contact</mj-navbar-link>
							</mj-navbar>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('About', $html);
		$this->assertStringContains('Contact', $html);
		$this->assertStringContains('href="/about"', $html);
		$this->assertStringContains('mj-inline-links', $html);
		// headStyle CSS for hamburger is always injected
		$this->assertStringContains('mj-menu-checkbox', $html);
		$html_node = $this->_mjml_node($src);
		$this->assertHtmlEquals($html_node, $html);
	}

	function test_base_url(){
		$src = '
			<mjml>
				<mj-body>
					<mj-section>
						<mj-column>
							<mj-navbar base-url="https://example.com">
								<mj-navbar-link href="/page">Page</mj-navbar-link>
							</mj-navbar>
						</mj-column>
					</mj-section>
				</mj-body>
			</mjml>
		';
		$html = Yarri\Mjml::Mjml2Html($src);
		$this->assertStringContains('href="https://example.com/page"', $html);
	}
}
