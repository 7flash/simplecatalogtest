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
		if ($oScheme = $this->Viewer_GetSmartyObject()->getTemplateVars('oScheme')) {
			$this->Viewer_Assign('aCategoryTree', $this->PluginSimplecatalog_Category_MyBuildCategoryTreeForScheme($oScheme));
			$this->Viewer_Assign('aProductFilterData', $this->PluginSimplecatalog_Product_PrepareProductFilterToDisplayFieldsInFormForScheme($oScheme));
		}
	}

}

?>