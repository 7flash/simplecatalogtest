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
		$this->AddHook('template_main_menu_item', 'MenuMainItem');
	}


	/**
	 * Добавление пунктов в меню "написать"
	 * 
	 * @return bool
	 */
	public function WriteItem() {
		
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
		
		$this->Viewer_Assign(
			'aSchemesMenuItems',
			$this->PluginSimplecatalog_Scheme_MyGetSchemeItemsByMenuMainAddLinkAndActive(
				PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
				PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED
			)
		);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'navs/menu.main_item.tpl');
	}

}

?>