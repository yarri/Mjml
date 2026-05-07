<?php
use Yarri\Mjml\Utils;

class TcUtils extends TcBase {

	function test_camelize(){
		// základní konverze MJML tag názvů na PHP třídy
		$this->assertEquals('MjText',             Utils::camelize('mj_text'));
		$this->assertEquals('MjSection',          Utils::camelize('mj_section'));
		$this->assertEquals('MjAccordionElement', Utils::camelize('mj_accordion_element'));
		$this->assertEquals('MjCarouselImage',    Utils::camelize('mj_carousel_image'));
		$this->assertEquals('MjSocialElement',    Utils::camelize('mj_social_element'));
		$this->assertEquals('MjNavbarLink',       Utils::camelize('mj_navbar_link'));

		// jednoslovný vstup
		$this->assertEquals('Hello', Utils::camelize('hello'));

		// prázdný řetězec
		$this->assertEquals('', Utils::camelize(''));
	}

	function test_h(){
		// základní escapování
		$this->assertEquals('&lt;div&gt;',        Utils::h('<div>'));
		$this->assertEquals('&quot;hello&quot;',  Utils::h('"hello"'));
		$this->assertEquals('&#039;world&#039;',  Utils::h("'world'"));
		$this->assertEquals('a &amp; b',          Utils::h('a & b'));

		// neřetězcové typy se přetypují
		$this->assertEquals('42',   Utils::h(42));
		$this->assertEquals('3.14', Utils::h(3.14));
		$this->assertEquals('1',    Utils::h(true));
		$this->assertEquals('',     Utils::h(false));

		// bez speciálních znaků se nic nemění
		$this->assertEquals('hello world', Utils::h('hello world'));
	}
}
