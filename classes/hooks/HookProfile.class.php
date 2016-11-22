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

class PluginSimplecatalog_HookProfile extends Hook {


	public function RegisterHook() {
		$this->AddHook('template_profile_whois_item_end', 'ProfileWhois');
	}


	/**
	 * Вывод последних продуктов на главной странице профиля пользователя
	 *
	 * @param $aVars
	 * @return bool
	 */
	public function ProfileWhois($aVars) {
		
		if (!isset($aVars) or !isset($aVars['oUserProfile'])) return false;
		
		$oForUser = $aVars['oUserProfile'];
		/*
		 * у которых есть продукты
		 */
		$aSchemes = array();
		foreach ($this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems() as $oScheme) {
			/*
			 * если не нужно показывать продукты этой схемы в профиле пользователя
			 */
			if ($oScheme->getProfileShowLastProducts() != PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED) {
				continue;
			}
			
			if (!$aProducts = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndUserAndSchemeAndPageAndPerPageAndSortOrder(
				array(
					PluginSimplecatalog_ModuleProduct::MODERATION_DONE
				),
				$oForUser,
				$oScheme,
				/*
				 * пагинация будет перекрыта ниже
				 */
				0,
				0,
				$this->PluginSimplecatalog_Product_GetDefaultProductSortingOrder(),
				array(
					/*
					 * перекрыть пагинацию чтобы использовать лимит без подсчета общего количества продуктов пользователя
					 */
					'#page' => null,
					/*
					 * получить максимум 3 продукта
					 */
					'#limit' => array(0, 3),
					'#with' => array('scheme')
				)
			)) {
				continue;
			}
			$oScheme->setProductsOfThisUser($aProducts);
			$aSchemes[] = $oScheme;
		}
		$this->Viewer_Assign('aSchemesOfThisUser', $aSchemes);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'actions/ActionProfile/items_view.tpl');
	}
	
}

?>