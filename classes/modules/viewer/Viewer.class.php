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
 * Работа с вьюером
 *
 */

class PluginSimplecatalog_ModuleViewer extends PluginSimplecatalog_Inherit_ModuleViewer {


	/**
	 * Инициализация вьюера
	 *
	 * @param bool $bLocal		локальная ли копия вьюера это
	 * @return bool
	 */
	public function Init($bLocal = false) {
		parent::Init($bLocal);

		
		/*
		 * добавить директорию с плагинами для Smarty
		 */
		$this->AddSmartyPluginsDir($this->PluginSimplecatalog_Tools_GetSmartyPluginsPath());
	}


	/**
	 * Добавить директорию с плагинами для Smarty
	 * 
	 * @param string $sDir		директория
	 * @return bool
	 */
	protected function AddSmartyPluginsDir($sDir) {
		if (!is_dir($sDir)) {
			return false;
		}
		$this->GetSmartyObject()->addPluginsDir($sDir);
		return true;
	}


	/**
	 * Добавить новые значения в переменную реквеста вьюера (_aRequest) на этапе вывода шаблона (например, при вызове через шаблонный хук)
	 *
	 * @param array $aData		добавляемые в _aRequest новые значения, где ключ - имя переменной
	 */
	public function AppendRequestData($aData) {
		/*
		 * docs: http://www.smarty.net/docs/en/api.append.tpl
		 */
		$this->GetSmartyObject()->append('_aRequest', $aData, true);
	}

}

?>