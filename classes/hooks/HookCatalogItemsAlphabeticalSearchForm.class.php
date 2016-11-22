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

class PluginSimplecatalog_HookCatalogItemsAlphabeticalSearchForm extends Hook {


	public function RegisterHook() {
		$this->AddHook('template_sc_product_items_search_form_after', 'ItemsAlphabeticalSearchForm');
	}


	/**
	 * Вывод алфавитного поиска
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ItemsAlphabeticalSearchForm($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];
		/*
		 * включен ли алфавитный поиск
		 */
		if (!$oScheme->getShowFirstLetterGroupsEnabled()) {
			return false;
		}
		/*
		 * получить алфавит из первых букв заголовков продуктов
		 */
		if (!$aSchemeAlphabeticalSearchItems = $this->PluginSimplecatalog_Product_MyGetProductFirstLetterGroupsByScheme($oScheme)) {
			return false;
		}

		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('oScheme', $oScheme);
		$oViewer->Assign('aSchemeAlphabeticalSearchItems', $aSchemeAlphabeticalSearchItems);
		return $oViewer->Fetch($this->PluginSimplecatalog_Scheme_GetTemplatePathByScheme($oScheme, 'items/search/alphabetical.form.tpl'));
	}
	
}
