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

class PluginSimplecatalog_HookShortCatalogUrlsAssigner extends Hook {

	public function RegisterHook() {
		$this->AddHook('engine_init_complete', 'EngineInitComplete');
		$this->AddHook('sc_scheme_add_after', 'SchemeItemUpdateAfter');
		$this->AddHook('sc_scheme_delete_after', 'SchemeItemUpdateAfter');
	}


	public function EngineInitComplete() {
		/*
		 * проверить настройки и файл для коротких урлов каталогов
		 */
		$this->CheckForShortUrls();
	}


	/**
	 * Проверить работу коротких урлов для каталогов
	 */
	protected function CheckForShortUrls() {
		/*
		 * включена ли поддержка коротких урлов для каталогов
		 */
		if (Config::Get('plugin.simplecatalog.urls.catalog.enable_short_urls')) {
			$sDatFile = $this->GetUrlsDatFilename();
			if (!file_exists($sDatFile)) {
				/*
				 * создать файл с урлами каталогов
				 */
				if ($this->AssignShortUrlsIntoDatFile($sDatFile)) {
					/*
					 * сделать редирект т.к. правила вступят в силу после перезагрузки движка
					 */
					Router::Location(Config::Get('path.root.web') . $_SERVER['REQUEST_URI']);
				}
			}
		}
	}


	/**
	 * Добавить урлы активных схем в указанный файл
	 *
	 * @param $sDatFile		файл, в котором урлы будут записаны каждая с новой строки
	 * @return bool
	 */
	protected function AssignShortUrlsIntoDatFile($sDatFile) {
		
		$aSchemeUrls = array();
		foreach($this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems() as $oScheme) {
			$aSchemeUrls[] = $oScheme->getSchemeUrl();
		}
		if (@file_put_contents($sDatFile, implode("\n", $aSchemeUrls)) === false) {
			/*
			 * показать сообщение только админам и не аякс запросам (чтобы можно было провести авторизацию)
			 */
			if ($oUserCurrent = $this->User_GetUserCurrent() and $oUserCurrent->isAdministrator() and !Router::GetIsAjaxRequest()) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.cant_write_short_urls_to_dat_file'), $this->Lang_Get('error'), true);
			}
			return false;
		}
		return true;
	}


	/**
	 * После сохранения схемы через веб-интерфейс
	 *
	 * @param $aData		параметры хука
	 */
	public function SchemeItemUpdateAfter($aData) {
		/*
		 * включена ли поддержка коротких урлов для каталогов
		 */
		if (Config::Get('plugin.simplecatalog.urls.catalog.enable_short_urls')) {
			$sDatFile = $this->GetUrlsDatFilename();
			/*
			 * удалить текущие сохраненные урлы
			 */
			$this->PluginSimplecatalog_File_RemoveFile($sDatFile);
			/*
			 * создать файл с урлами каталогов
			 */
			$this->AssignShortUrlsIntoDatFile($sDatFile);
		}
	}


	/**
	 * Получить полный путь к файлу с именами урлов схем для загрузки в роутер
	 *
	 * @return string
	 */
	protected function GetUrlsDatFilename() {
		return Config::Get('sys.cache.dir') . 'sc_catalog_urls.dat';
	}


}
