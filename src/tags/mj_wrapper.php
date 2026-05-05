<?php
namespace Yarri\Mjml\Tags;

class MjWrapper extends MjSection {

	static $componentName = 'mj-wrapper';

	function renderWrappedChildren(){
		$containerWidth = $this->context->containerWidth;
		$wrapper = $this;

		return $this->renderChildren(function($component) use ($containerWidth, $wrapper){
			return "
			<!--[if mso | IE]>
				<tr>
					<td {$component->htmlAttributes([
						'align' => $component->getAttribute('align'),
						'class' => $wrapper->suffixCssClasses($component->getAttribute('css-class'), 'outlook'),
						'width' => $containerWidth,
					])}>
			<![endif]-->
				{$component->render()}
			<!--[if mso | IE]>
					</td>
				</tr>
			<![endif]-->
			";
		});
	}
}
