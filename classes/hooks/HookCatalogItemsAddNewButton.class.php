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

class PluginSimplecatalog_HookCatalogItemsAddNewButton extends Hook {


	public function RegisterHook() {
		$this->AddHook('template_sc_product_items_before', 'ItemsAddNewButton', __CLASS__, 50000);
	}


	/**
	 * Кнопка создания нового продукта
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ItemsAddNewButton($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];
		$oUserCurrent = $this->User_GetUserCurrent();
		/*
		 * показывать ли пункт в меню создать и есть ли права на создание продуктов
		 */
		if (!$oScheme->getMenuAddTopicCreateEnabled() or !$oUserCurrent or !$oUserCurrent->getCanAddNewProductsInScheme($oScheme)) {
			return false;
		}

		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('oScheme', $oScheme);
		return $oViewer->Fetch($this->PluginSimplecatalog_Scheme_GetTemplatePathByScheme($oScheme, 'items/add_button.tpl'));
	}
	
}
