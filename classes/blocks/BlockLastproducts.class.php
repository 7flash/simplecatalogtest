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
 * Вывод схем и их последних продуктов в сайдбаре
 *
 */

class PluginSimplecatalog_BlockLastproducts extends Block {


	public function Exec() {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		/*
		 * получить вывод схем и их последних продуктов, которые нужно показать в сайдбаре
		 */
		if ($aItems = $this->PluginSimplecatalog_Product_AssignSchemesWithLastProductsForActiveSchemesByShowLastProductsType(
			PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_IN_SIDEBAR
		)) {
			$this->Viewer_Assign('aSC_BlockLastProducts', $aItems);
		}
	}
	
}

?>