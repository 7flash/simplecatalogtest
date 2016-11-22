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
 * Класс готовых основных хтмл тегов
 *
 */

class HtmlTagReady extends HtmlTag {

	/**
	 * Создать хтмл-строку ссылки
	 *
	 * @param       $sLink			ссылка
	 * @param       $sText			текст ссылки
	 * @param array $aAttr			дополнительные атрибуты ссылки
	 * @return string
	 */
	static public function A($sLink, $sText, $aAttr = array()) {
		return self::Tag('a', array_merge(array('href' => $sLink), $aAttr), false, $sText);
	}


	/**
	 * Создать хтмл-строку изображения
	 *
	 * @param       $sSrc			путь к изображению
	 * @param int   $iWidth			ширина
	 * @param int   $iHeight		высота
	 * @param array $aAttr			дополнительные атрибуты изображения
	 * @return string
	 */
	static public function Img($sSrc, $iWidth = 800, $iHeight = 600, $aAttr = array()) {
		return self::Tag('img', array_merge(array('src' => $sSrc, 'width' => $iWidth, 'height' => $iHeight), $aAttr));
	}


	/**
	 * Создать хтмл-строку переноса строки
	 *
	 * @return string
	 */
	static public function Br() {
		return self::Tag('br');
	}

}
