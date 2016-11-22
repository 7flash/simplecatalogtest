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

class PluginSimplecatalog_HookMenu extends Hook {

	public function RegisterHook() {
		$this->AddHook('template_write_item', 'WriteItem');
		$this->AddHook('template_catalog_scheme', 'MenuMainItem');
	}


	/**
	 * Добавление пунктов в меню "написать"
	 * 
	 * @return bool
	 */
	public function WriteItem() {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$this->Viewer_Assign(
			'aSchemesMenuItems',
			$this->PluginSimplecatalog_Scheme_MyGetSchemeItemsByMenuAddTopicCreateAndActive(
				PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
				PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED
			)
		);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'navs/menu.write_item.tpl');
	}


	/**
	 * Добавление пунктов в главное меню
	 *
	 * @return bool
	 */
	public function MenuMainItem() {
		if (!SCRootStorage::IsInit()) {
			return false;
		}

		$aSchemes = $this->PluginSimplecatalog_Scheme_MyGetSchemeItemsByMenuMainAddLinkAndActive(
				PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
				PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED
		);

		$aMenuItems = array();
		foreach ($aSchemes as $oScheme)  {
			$iCount = $this->PluginSimplecatalog_Category_getCountItemsByFilter(array('target_id'=>$oScheme->getId(), 'parent_id' => 0), 'PluginSimplecatalog_ModuleCategory_EntityCategory');

			$col = array(
					'0' => 12,
					'1' => 12,
					'2' => 6,
					'3' => 4,
					'4' => 3,
					'5' => 2,
					'6' => 2
			);

			$aMenuItems[$oScheme->getId()]['scheme']=$oScheme;
			$aMenuItems[$oScheme->getId()]['categories']=$this->PluginSimplecatalog_Category_MyBuildCategoryTreeForScheme($oScheme);

			$aMenuItems[$oScheme->getId()]['col_count']=$col[$iCount];




		}
		$this->Viewer_Assign(
			'aSchemesMenuItems', $aMenuItems
		);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'navs/menu.main_item.tpl');
	}

}

?>