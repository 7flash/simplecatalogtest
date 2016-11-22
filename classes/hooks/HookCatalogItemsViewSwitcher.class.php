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

class PluginSimplecatalog_HookCatalogItemsViewSwitcher extends Hook {


	public function RegisterHook() {
		$this->AddHook('template_sc_product_items_before', 'SchemeTemplatesSwitch', __CLASS__, PHP_INT_MAX);
		$this->AddHook('template_sc_product_items_after', 'SchemeTemplatesSwitch', __CLASS__, PHP_INT_MAX);
	}


	/**
	 * Переключатель шаблонов схемы
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function SchemeTemplatesSwitch($aData) {
		
		$oScheme = $aData['oScheme'];

		if (!$oScheme->getTemplateSwitchEnabled()) {
			return false;
		}
		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('oScheme', $oScheme);
		return $oViewer->Fetch($this->PluginSimplecatalog_Scheme_GetTemplatePathByScheme($oScheme, 'items/templates_switch.tpl'));
	}
	
}
