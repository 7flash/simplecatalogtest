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
 * Загрузка текстовок для JS
 *
 */

class PluginSimplecatalog_HookAddLangJs extends Hook {


	public function RegisterHook() {
		$this->AddHook('engine_init_complete', 'EngineInitComplete');
	}


	public function EngineInitComplete() {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$this->Lang_AddLangJs(array(
			/*
			 * загрузка дней и месяцев для выбора дат
			 */
			'plugin.simplecatalog.datepicker.days',
			'plugin.simplecatalog.datepicker.months',
		));
	}
	
}

?>