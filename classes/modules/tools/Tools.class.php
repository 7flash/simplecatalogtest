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

class PluginSimplecatalog_ModuleTools extends Module {


	public function Init() {}


	/**
	 * Получить серверный путь
	 *
	 * @param $sPath    путь
	 * @return mixed
	 */
	public function GetServerPath($sPath) {
		return $this->Image_GetServerPath($sPath);
	}


	/**
	 * Получить веб путь
	 *
	 * @param $sPath    путь
	 * @return mixed
	 */
	public function GetWebPath($sPath) {
		return $this->Image_GetWebPath($sPath);
	}


	/**
	 * Получить урл проверенного реферера или урл страницы по-умолчанию, исключая возможность зацикливания редиректа на самого себя
	 *
	 * @param bool $bPreventCyclicalRedirect		запретить зацикливание страницы на саму себя
	 * @return string								урл для редиректа
	 */
	protected function GetCorrectRefererOrDefaultPageUrl($bPreventCyclicalRedirect = true) {
		/*
		 * итоговый урл
		 */
		$sRedirectUrl = null;
		$sRefererRaw = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		/*
		 * получить домен реферера
		 */
		$sRefererHost = @parse_url($sRefererRaw, PHP_URL_HOST);
		/*
		 * получить домен сайта из конфига
		 * tip: не доверять $_SERVER['HTTP_HOST'] т.к. он небезопасен (присылается клиентом)
		 */
		$sOriginalHost = @parse_url(Config::Get('path.root.web'), PHP_URL_HOST);
		/*
		 * корректно ли получены домены и принадлежит ли домен реферера домену или субдомену сайта
		 */
		if ($sRefererHost and $sOriginalHost and (strcasecmp($sRefererHost, $sOriginalHost) == 0 or preg_match('#\.' . quotemeta($sOriginalHost) . '$#i', $sRefererHost))) {
			$sRedirectUrl = $sRefererRaw;
		}
		/*
		 * проверить чтобы не было зацикливания редиректа страницы на саму себя
		 */
		if ($bPreventCyclicalRedirect and $sRedirectUrl) {
			if ($_SERVER['REQUEST_URI'] == str_replace(Config::Get('path.root.web'), '', $sRedirectUrl)) {
				$sRedirectUrl = null;
			}
		}
		/*
		 * если реферера нету - открыть страницу по-умолчанию
		 */
		if (!$sRedirectUrl) {
			$sRedirectUrl = Router::GetPath('default');
		}
		return $sRedirectUrl;
	}


	/**
	 * Выполнить редирект на проверенный реферер
	 *
	 * @param bool $bPreventCyclicalRedirect		запретить зацикливание страницы на саму себя
	 */
	public function RedirectToReferer($bPreventCyclicalRedirect = true) {
		Router::Location($this->GetCorrectRefererOrDefaultPageUrl($bPreventCyclicalRedirect));
	}


	/**
	 * Возвращает путь к плагинам для смарти
	 *
	 * @return string
	 */
	public function GetSmartyPluginsPath() {
		return Plugin::GetPath(__CLASS__) . 'include/smarty/';
	}


	/**
	 * Получить значение из фильтра (массива-переменной "filter" из реквеста) или весь фильтр
	 *
	 * @param string $sName			имя ключа из массива фильтра или null для получения всего фильтра
	 * @return mixed|array|null		значение
	 */
	public function GetDataFromFilter($sName = null) {
		/*
		 * получить фильтр, хранящий в себе все параметры
		 */
		if ($aFilter = getRequest('filter') and is_array($aFilter)) {
			/*
			 * если нужны все значения фильтра
			 */
			if (!$sName) {
				return $aFilter;
			}
			/*
			 * если нужно выбрать одно значение из фильтра
			 */
			if ($sName and isset($aFilter[$sName]) and $aFilter[$sName] !== '') {
				return $aFilter[$sName];
			}
		}
		return null;
	}

}
