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

class PluginSimplecatalog_HookDeferredProductsPublisher extends Hook {


	public function RegisterHook() {
		$this->AddHook('engine_init_complete', 'EngineInitComplete');
	}


	public function EngineInitComplete() {
		
		/*
		 * публикация отложенных продуктов из всех каталогов
		 */
		if ($iCount = $this->PluginSimplecatalog_Product_PerformDeferredProductsPublishing() and $this->User_GetIsAdmin()) {
			$this->Message_AddNotice(
				$this->Lang_Get('plugin.simplecatalog.notices.products.deferred.products_published', array('count' => $iCount)),
				$this->Lang_Get('plugin.simplecatalog.Title')
			);
		}
	}


}

?>