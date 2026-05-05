<?php
namespace Yarri\Mjml\Tags;

class MjRaw extends _Tag {

	static $componentName = 'mj-raw';

	static $allowedAttributes = [
		'position' => 'enum(file-start)',
	];

	function render(){
		return $this->getContent();
	}
}
