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


if (!function_exists('my_array_column')) {
	/**
	 * Получить из массива ассоциативных массивов значения по указанному имени столбца
	 * tip: аналог array_column из пхп 5.5
	 *
	 * @param array $aArray         массив ассоциативных массивов
	 * @param int   $mColumnKeyName ключ ассоциативного подмассива для получения значения
	 * @param int   $mIndexKeyName  ключ ассоциативного подмассива для получения значения, которое будет использовано в качестве ключа для устанавливаемого значения в новом массиве
	 * @return array                массив значений из указанного столбца
	 */
	function my_array_column($aArray, $mColumnKeyName, $mIndexKeyName = null) {
		$aData = array();
		foreach($aArray as $aRow) {
			/*
			 * есть ли значение
			 */
			if (!isset($aRow[$mColumnKeyName])) {
				continue;
			}
			$mValue = $aRow[$mColumnKeyName];
			/*
			 * если имя ключа ассоциативного массива указано и это значение существует
			 */
			if (!is_null($mIndexKeyName) and isset($aRow[$mIndexKeyName])) {
				/*
				 * добавить нужное значение с указанием ключа как значения другого ключа ассоциативного массива
				 */
				$aData[$aRow[$mIndexKeyName]] = $mValue;
			} else {
				/*
				 * просто добавить значение
				 */
				$aData[] = $mValue;
			}
		}
		return $aData;
	}
}

?>