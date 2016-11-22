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

class PluginSimplecatalog_BlockInthesecategories extends Block {

	public function Exec() {
		/*
		 * можно найти продукт через УРЛ, но данный метод уменьшит количество запросов к БД
		 */
		if ($oProduct = $this->Viewer_GetSmartyObject()->getTemplateVars('oProduct') and $oScheme = $this->Viewer_GetSmartyObject()->getTemplateVars('oScheme')) {
			$this->Viewer_Assign('aInTheseCategoriesProducts', $this->PluginSimplecatalog_Product_MyGetProductItemsByTypeMoreFromProductCategories($oProduct, $oScheme));
		}
	}
	
}

?>