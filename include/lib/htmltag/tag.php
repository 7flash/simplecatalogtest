<?php
/**
 * Simplecatalog plugin
 *
 * @copyright Serge Pustovit (PSNet), 2008 - 2015
 * @author    Serge Pustovit (PSNet) <light.feel@gmail.com>
 *
 * @link      http://psnet.lookformp3.net
 * @link      http://livestreet.ru/profile/PSNet/
 * @link      https://catalog.livestreetcms.com/profile/PSNet/
 * @link      http://livestreetguide.com/developer/PSNet/
 */

/*
 *
 * Класс для построения хтмл строки тега с нужными атрибутами
 *
 */

class HtmlTag {

	/*
	 * Разделитель для атрибутов тегов
	 */
	static public $sSeparator = ' ';


	/**
	 * Создать хтмл-строку тега
	 *
	 * @param       $sTag			тег
	 * @param array $aAttr			массив атрибутов тега (атрибут => значение атрибута)
	 * @param bool  $bShortTag		короткий ли это тег
	 * @param null  $sText			текст для не коротких тегов
	 * @return string				хтмл строка
	 */
	static public function Tag($sTag, $aAttr = array(), $bShortTag = true, $sText = null) {
		$sResult = '<' . $sTag;
		foreach($aAttr as $sAttr => $sValue) {
			$sResult .= self::$sSeparator . $sAttr . '="' . addslashes($sValue) . '"';
		}
		if ($bShortTag) {
			return $sResult . self::$sSeparator . '/>';
		}
		return $sResult . '>' . $sText . '</' . $sTag . '>';
	}

}
