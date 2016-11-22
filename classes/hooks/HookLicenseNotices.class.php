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
 * Оповещение пользователя о статусе лицензии
 *
 */

class PluginSimplecatalog_HookLicenseNotices extends Hook {


	public function RegisterHook() {
		$this->AddHook('engine_init_complete', 'EngineInitComplete');
	}


	public function EngineInitComplete() {
		$bSuccess = true;
		if (SCRootStorage::IsLicenseExpired()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.licenses.expired'), $this->Lang_Get('error'));
			$bSuccess = false;
		}
		if (!SCRootStorage::IsLicenseActive()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.licenses.unactive'), $this->Lang_Get('error'));
			$bSuccess = false;
		}
		/*
		 * вывести контакты для связи
		 */
		if (!$bSuccess and $this->User_GetUserCurrent() and $this->User_GetUserCurrent()->isAdministrator()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.licenses.contacts'));
		}
	}
	
}

?>