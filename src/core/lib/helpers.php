<?php
namespace Yarri\Mjml\Core\Lib;

class Helpers {

	static function widthParser($width, $options = []) {
		$options += [
			"parseFloatToInt" => true,
		];

		$unitRegex = '/[\d.,]*(\D*)$/';
		preg_match($unitRegex,$width,$matches);
		$widthUnit = $matches[1];

		$parseInt = function($width){
			return (int)$width;
		};
		$parseFloat = function($width){
			return (float)$width;
		};
	 
		$unitParsers = [
			"default" => $parseInt,
			"px" => $parseInt,
			"%" => $options["parseFloatToInt"] ? $parseInt : $parseFloat
		];
		$parser = isset($unitParsers[$widthUnit]) ? $unitParsers[$widthUnit] : $unitParsers["default"];
		return [
			"parsedWidth" => $parser($width),
			"unit" => strlen($widthUnit) ? $widthUnit : "px"
		];
	}

}
