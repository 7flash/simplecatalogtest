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
 * Вывод схем и их последних продуктов перед контентом топиков
 *
 */

class PluginSimplecatalog_HookLastProducts extends Hook {


	public function RegisterHook() {
		$this->AddHook('template_content_begin', 'TemplateContentBegin');
	}


	public function TemplateContentBegin() {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		/*
		 * где показывать блок
		 */
		if (!in_array(Router::GetAction(), Config::Get('plugin.simplecatalog.product.actions_to_show_last_products_before_content'))) return false;
		/*
		 * получить вывод схем и их последних продуктов, которые нужно показать перед топиками
		 */
		if ($aItems = $this->PluginSimplecatalog_Product_AssignSchemesWithLastProductsForActiveSchemesByShowLastProductsType(
			PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_BEFORE_CONTENT
		)) {
			$this->Viewer_Assign('aSC_HookLastProducts', $aItems);
			return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'helpers/products/last_products.hook_wrapper.tpl');
		}
		return false;
	}
	
}

?>