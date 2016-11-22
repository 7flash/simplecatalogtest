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

class PluginSimplecatalog_HookProfileMenuCreated extends Hook {

	public function RegisterHook() {
		$this->AddHook('template_menu_profile_created_item', 'ProfileCreatedItem');
	}


	/**
	 * Вывод в подменю страницы "публикации" профиля пользователя списка схем и количества публикаций пользователя в каждой
	 *
	 * @param $aVars
	 * @return bool
	 */
	public function ProfileCreatedItem($aVars) {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		if (!isset($aVars) or !isset($aVars['oUserProfile'])) {
			return false;
		}

		$oForUser = $aVars['oUserProfile'];
		$aSchemes = array();
		foreach ($this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems() as $oScheme) {
			/*
			 * если не нужно показывать созданные продукты этой схемы в "публикациях" профиля пользователя
			 */
			if ($oScheme->getProfileShowCreatedProducts() != PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED) {
				continue;
			}
			
			if (!$iCount = $this->PluginSimplecatalog_Product_MyGetCountProductItemsBySchemeIdAndUserIdAndModerationIn(
				$oScheme->getId(),
				$oForUser->getId(),
				array(
					PluginSimplecatalog_ModuleProduct::MODERATION_DONE
				)
			)) {
				continue;
			}
			$oScheme->setProductsCount($iCount);
			$aSchemes[] = $oScheme;
		}
		$this->Viewer_Assign('aSchemesList', $aSchemes);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'actions/ActionProfile/created_item.tpl');
	}
	
}

?>