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

class PluginSimplecatalog_BlockProductfilter extends Block {


	public function Exec() {
		/*
		 * можно найти схему через УРЛ, но данный метод уменьшит количество запросов к БД
		 */
		if ($oScheme = $this->Viewer_GetSmartyObject()->getTemplateVars('oScheme') and SCRootStorage::IsInit()) {
			$this->Viewer_Assign('aCategoryTree', $this->PluginSimplecatalog_Category_MyBuildCategoryTreeForScheme($oScheme));

			$aMin = $this->PluginSimplecatalog_Product_MyGetProductItemsByFilter(array('#order'=>array('price'=>'asc'), '#linit' => array(0, 1), 'scheme_id'=>$oScheme->getId()));
			$aMax = $this->PluginSimplecatalog_Product_MyGetProductItemsByFilter(array('#order'=>array('price'=>'desc'), '#linit' => array(0, 1), 'scheme_id'=>$oScheme->getId()));

			$this->Viewer_Assign('iMinPrice',$aMin[0]->getPrice());
			$this->Viewer_Assign('iMaxPrice',$aMax[0]->getPrice());

			/***/
			$aUserIds = array();
			$aCityIds = array();
			$aProducts = $this->PluginSimplecatalog_Product_MyGetProductItemsByFilter(array('scheme_id'=>$oScheme->getId()));
			foreach ($aProducts as $aProduct){
				if(empty($aCityIds[$aProduct->getCity()])){
					$aCityIds[$aProduct->getCity()]=$aProduct->getCity();
				}
				if(empty($aUserIds[$aProduct->getUserId()])){
					$aUserIds[$aProduct->getUserId()]=$aProduct->getUserId();
				}
			}

			$aProvider = $this->PluginProvider_Provider_GetProviderItemsByFilter( array('user_type'=>'provider', 'user_id IN'=>$aUserIds, '#order'=>array('rating'=>'desc')));

			$aCites = $this->PluginProvider_Provider_GetCityItemsByFilter(array( 'id IN' => $aCityIds, '#order'=>array('items_count'=>'desc')));

			$this->Viewer_Assign('aProvider', $aProvider);
			$this->Viewer_Assign('aCites', $aCites);


			$this->Viewer_Assign('aProductFilterData', $this->PluginSimplecatalog_Product_PrepareProductFilterToDisplayFieldsInFormForScheme($oScheme));
		}
	}

}

?>