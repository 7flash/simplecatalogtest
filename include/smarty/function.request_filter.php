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


/**
 * Фильтр для построения строки запроса с автоматическим достроением части запроса из переменной filter[] реквеста
 *
 *
 * Пример со всеми параметрами:
 *
 *		{request_filter name=array('order', 'way') value=array('login', 'desc') prefix="?" separator="&"}
 *
 * возвратит строку:
 *
 *		?filter[order]=login&filter[way]=desc&filter[q]=значение_q_из_реквеста&filter[field]=значение_field_из_реквеста
 *
 *
 * @param $aParams		параметры
 * @param $oSmarty		объект смарти
 * @return string		строка запроса
 */
function smarty_function_request_filter($aParams, &$oSmarty) {
	$aFilter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array();

	/*
	 * если указаны дополнительные значения для фильтра
	 */
	if (isset($aParams['name'])) {
		/*
		 * список ключей должен быть массивом
		 */
		if (!is_array($aParams['name'])) {
			$aParams['name'] = (array) $aParams['name'];
		}

		/*
		 * если задана установка значений
		 */
		if (isset($aParams['value'])) {
			/*
			 * списки значений ключей должны быть массивом
			 */
			if (!is_array($aParams['value'])) {
				$aParams['value'] = (array) $aParams['value'];
			}
			/*
			 * установить все пары "ключ => значение" в фильтре
			 */
			foreach ($aParams['name'] as $iKey => $sVal) {
				$aFilter[$sVal] = $aParams['value'][$iKey];
			}

		} else {
			/*
			 * удалить значения из фильтра по имени ключей
			 */
			foreach ($aParams['name'] as $sVal) {
				unset($aFilter[$sVal]);
			}
		}
	}

	/*
	 * все значения хранятся в массиве filter реквеста
	 */
	$aResult = array('filter' => $aFilter);

	/*
	 * для построения запроса
	 */
	$sPrefix = isset($aParams['prefix']) ? $aParams['prefix'] : '?';
	$sSeparator = isset($aParams['separator']) ? $aParams['separator'] : '&';

	/*
	 * построить строку
	 */
	$sResult = http_build_query($aResult, '', $sSeparator);
	return $sResult ? $sPrefix . $sResult : '';
}

?>