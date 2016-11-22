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
 * Хук вызывается из меню menu.content.items.tpl для вывода количества элементов в вкладках
 */

class PluginSimplecatalog_HookCatalogItemsMenuContentItemsCountPerformers extends Hook {

	
	public function RegisterHook() {
		$this->AddHook('template_sc_product_items_menu_content_items_moderation_needed_products_count', 'MenuItemsModerationNeededProductsCount');
		$this->AddHook('template_sc_product_items_menu_content_items_my_products_count', 'MenuItemsMyProductsCount');
		$this->AddHook('template_sc_product_items_menu_content_items_drafts_count', 'MenuItemsDraftsCount');
		$this->AddHook('template_sc_product_items_menu_content_items_map_items_count', 'MenuItemsMapItems');
	}


	/**
	 * Количество продуктов, которые нужно промодерировать
	 *
	 * @param $aData		параметры хука
	 * @return bool|int|null
	 */
	public function MenuItemsModerationNeededProductsCount($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];
		if (!is_null($iCurrentValueExists = $aData['iModerationNeededProducts'])) {
			return $iCurrentValueExists;
		}
		return $this->_CalcModerationNeededProductsCountByScheme($oScheme, $this->User_GetUserCurrent());
	}


	/**
	 * Количество "моих" продуктов
	 *
	 * @param $aData		параметры хука
	 * @return bool|int|null
	 */
	public function MenuItemsMyProductsCount($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];
		if (!is_null($iCurrentValueExists = $aData['iMyProducts'])) {
			return $iCurrentValueExists;
		}
		return $this->_CalcMyProductsCountByScheme($oScheme, $this->User_GetUserCurrent());
	}


	/**
	 * Количество черновиков
	 *
	 * @param $aData		параметры хука
	 * @return bool|int|null
	 */
	public function MenuItemsDraftsCount($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];
		if (!is_null($iCurrentValueExists = $aData['iDraftsProducts'])) {
			return $iCurrentValueExists;
		}
		return $this->_CalcDraftProductsCountByScheme($oScheme, $this->User_GetUserCurrent());
	}


	/**
	 * Количество меток на карте
	 *
	 * @param $aData		параметры хука
	 * @return bool|int|null
	 */
	public function MenuItemsMapItems($aData) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$oScheme = $aData['oScheme'];
		/*
		 * tip: метки на карте нужно загружать всегда т.к. эвент карт не загружает количество сам
		 * но для унификации оставлена эта проверка
		 */
		if (!is_null($iCurrentValueExists = $aData['iTotalMapItemsCount'])) {
			return $iCurrentValueExists;
		}
		return $this->_CalcAllMapItemsCountByScheme($oScheme, $this->User_GetUserCurrent());
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Посчитать количество продуктов для которых нужна модерация по схеме
	 *
	 * @param $oScheme		сущность схемы
	 * @param $oUserCurrent	сущность пользователя
	 * @return int|null
	 */
	private function _CalcModerationNeededProductsCountByScheme($oScheme, $oUserCurrent) {
		/*
		 * проверить права
		 */
		if ($oUserCurrent and $oUserCurrent->getUserCanModerateProductsBySchemeOrIsAdmin($oScheme)) {
			/*
			 * посчитать количество продуктов, которые нужно промодерировать
			 */
			return $this->PluginSimplecatalog_Product_MyGetCountProductItemsByModerationAndScheme(PluginSimplecatalog_ModuleProduct::MODERATION_NEEDED, $oScheme);
		}
		return null;
	}


	/**
	 * Посчитать количество продуктов, созданных текущим пользователем
	 *
	 * @param $oScheme		сущность схемы
	 * @param $oUserCurrent	сущность пользователя
	 * @return int|null
	 */
	private function _CalcMyProductsCountByScheme($oScheme, $oUserCurrent) {
		/*
		 * проверить права на создание продуктов в этой схеме
		 */
		if ($oUserCurrent and $oUserCurrent->getCanAddNewProductsInScheme($oScheme)) {
			/*
			 * посчитать количество продуктов, созданных пользователем
			 */
			return $this->PluginSimplecatalog_Product_MyGetCountProductItemsBySchemeIdAndUserIdAndModerationIn(
				$oScheme->getId(),
				$oUserCurrent->getId(),
				array(
					PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
					PluginSimplecatalog_ModuleProduct::MODERATION_NEEDED,
					PluginSimplecatalog_ModuleProduct::MODERATION_DEFERRED,
				)
			);
		}
		return null;
	}


	/**
	 * Посчитать количество черновиков, созданных текущим пользователем
	 *
	 * @param $oScheme		сущность схемы
	 * @param $oUserCurrent	сущность пользователя
	 * @return int|null
	 */
	private function _CalcDraftProductsCountByScheme($oScheme, $oUserCurrent) {
		/*
		 * проверить права на создание продуктов в этой схеме и разрешение на использование черновиков
		 */
		if ($oUserCurrent and $oUserCurrent->getCanAddNewProductsInScheme($oScheme) and $oScheme->getAllowDraftsEnabled()) {
			/*
			 * посчитать количество черновиков, созданных пользователем
			 */
			return $this->PluginSimplecatalog_Product_MyGetCountProductItemsBySchemeIdAndUserIdAndModerationIn(
				$oScheme->getId(),
				$oUserCurrent->getId(),
				array(
					PluginSimplecatalog_ModuleProduct::MODERATION_DRAFT
				)
			);
		}
		return null;
	}


	/**
	 * Посчитать количество всех меток на карте схемы
	 *
	 * @param $oScheme		сущность схемы
	 * @param $oUserCurrent	сущность пользователя
	 * @return int|null
	 */
	private function _CalcAllMapItemsCountByScheme($oScheme, $oUserCurrent) {
		if ($oScheme->getMapItemsEnabled()) {
			return $this->PluginSimplecatalog_Maps_MyGetSchemeMapItemsCountByScheme($oScheme);
		}
		return null;
	}
	
}
