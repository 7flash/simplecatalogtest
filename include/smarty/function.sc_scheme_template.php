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
 * Функция "sc_scheme_template" смарти (аналог include) для подключения файлов шаблонов из определенного схемой набора шаблонов схемы
 * (см. /templates/skin/default/scheme_templates/readme.txt)
 *
 * В обязательные параметры вызова входят:
 * 		scheme - сущность схемы по настройкам которой получить набор шаблонов
 * 		file - имя конкретного подключаемого файла
 *
 * @param array  $aParams параметры
 * @param Smarty $oSmarty объект смарти
 * @return string
 */
function smarty_function_sc_scheme_template($aParams, &$oSmarty) {
	if (!isset($aParams['scheme'])) {
		trigger_error('SC: scheme template function: "scheme" param needs to be set', E_USER_ERROR);
	}
	if (!isset($aParams['file'])) {
		trigger_error('SC: scheme template function: "file" param needs to be set', E_USER_ERROR);
	}
	$oScheme = $aParams['scheme'];
	$sFile = $aParams['file'];
	$oEngine = Engine::getInstance();
	/*
	 * удалить системные параметры
	 */
	unset($aParams['scheme'], $aParams['file']);
	/*
	 * получить полный путь к шаблону и проверить на существование
	 */
	$sPath = $oEngine->PluginSimplecatalog_Scheme_GetTemplatePathByScheme($oScheme, $sFile);

	/*
	 * возможно: исключить переменные родителя
	 * 		 	 перед тем как делать - решить вопрос о глобальных переменных (язык, например)
	 *
	 * $aSavedVars = $oSmarty->tpl_vars;
	 * $oSmarty->tpl_vars = null;
	 * getSubTemplate
	 * $oSmarty->tpl_vars = $aSavedVars;
	 */
	$sData = $oSmarty->getSubTemplate($sPath, $oSmarty->cache_id, $oSmarty->compile_id, $oSmarty->caching, $oSmarty->cache_lifetime, $aParams, Smarty::SCOPE_LOCAL);

	if (!$aParams['assign']) {
		return $sData;
	}
	$oSmarty->assign($aParams['assign'], $sData);
	return '';
}


?>