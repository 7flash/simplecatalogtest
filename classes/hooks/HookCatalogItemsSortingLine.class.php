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

class PluginSimplecatalog_HookCatalogItemsSortingLine extends Hook {


	public function RegisterHook() {
		$this->AddHook('template_sc_product_items_before', 'ItemsSortingLine');
	}


	/**
	 * Строка сортировки продуктов
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ItemsSortingLine($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];
		$aSortOrderData = $aData['aSortOrderData'];

		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('oScheme', $oScheme);
		$oViewer->Assign('aSortOrderData', $aSortOrderData);
		return $oViewer->Fetch($this->PluginSimplecatalog_Scheme_GetTemplatePathByScheme($oScheme, 'items/sorting_line.tpl'));
	}
	
}
