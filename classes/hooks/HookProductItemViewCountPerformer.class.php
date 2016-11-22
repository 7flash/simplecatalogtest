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
 * Обработка просмотров продукта
 *
 */

class PluginSimplecatalog_HookProductItemViewCountPerformer extends Hook {


	public function RegisterHook() {
		$this->AddHook('sc_product_item_view', 'ProductItemView');
		$this->AddHook('sc_product_item_delete_before', 'ProductItemDelete');
	}


	/**
	 * Хук эвента просмотра продукта
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ProductItemView($aData) {
		
		$oProduct = $aData['oProduct'];
		$oScheme = $aData['oScheme'];
		/*
		 * включены ли просмотры в схеме
		 */
		if (!$oScheme->getAllowCountViewsEnabled()) {
			return false;
		}
		/*
		 * исключить из просмотров админов, автора и пользователей, у которых есть права на редактирование продукта
		 */
		if (Config::Get('plugin.simplecatalog.count.product.views.exclude_managers') and $oUserCurrent = $this->User_GetUserCurrent() and $oUserCurrent->getCanManageProduct($oProduct)) {
			return false;
		}
		/*
		 * увеличение счетчика просмотров
		 */
		if (($mResult = $this->PluginSimplecatalog_Counter_IncreaseProductCounter($oProduct)) !== true) {
			$this->Message_AddError($mResult, $this->Lang_Get('error'));
		}
	}


	/**
	 * Хук удаления продукта
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ProductItemDelete($aData) {
		
		$oProduct = $aData['oProduct'];
		/*
		 * удалить просмотры продукта
		 */
		if ($oProduct->getScheme()->getAllowCountViewsEnabled()) {
			$this->PluginSimplecatalog_Counter_DeleteCounterByProduct($oProduct);
		}
	}


}

?>