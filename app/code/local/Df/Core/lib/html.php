<?php
/**
 * 2015-04-16
 * From now on you can pass an array as an attribute's value: @see \Df\Core\Html\Tag::getAttributeAsText()
 * It can be useful for attrivutes like `class`.
 * 2016-05-30 From now on $attrs could be a string. It is the same as ['class' => $attrs].
 * 2024-03-03 "Port `df_tag()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/448
 * @param string|array(string => string|string[]|int|null) $attrs [optional]
 * @param string|string[] $content [optional]
 * @param bool|null $multiline [optional]
 */
function df_tag(string $tag, $attrs = [], $content = '', $multiline = null):string {
	$t = new Tag($tag, is_array($attrs) ? $attrs : ['class' => $attrs], $content, $multiline); /** @vat Tag $t */
	return $t->render();
}