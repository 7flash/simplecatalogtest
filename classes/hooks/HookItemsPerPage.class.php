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

class PluginSimplecatalog_HookItemsPerPage extends Hook {


	public function RegisterHook() {
		$this->AddHook('template_sc_product_items_before', 'ItemsPerPageForm', __CLASS__, 100000);
	//	$this->AddHook('template_sc_product_items_after', 'ItemsPerPageForm', __CLASS__, 100000);
	}


	/**
	 * Выбор количества продуктов на страницу
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ItemsPerPageForm($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];

		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('oEntity', $oScheme);
		return $oViewer->Fetch($this->PluginSimplecatalog_Scheme_GetTemplatePathByScheme($oScheme, 'items/items_per_page.tpl'));
	}
	
}
