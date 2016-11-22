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
 * Добавление флага отображения кнопки корзины в тулбаре если есть продукты
 *
 */

class PluginSimplecatalog_HookCartButton extends Hook {


	public function RegisterHook() {
		$this->AddHook('engine_init_complete', 'EngineInitComplete');
	}


	public function EngineInitComplete() {
		
		/*
		 * есть ли в корзине продукты
		 */
		if ($this->PluginSimplecatalog_Shop_GetProductIdsFromCart()) {
			$this->Viewer_Assign('bSCToolbarShowCartButton', true);
		}
	}
	
}

?>