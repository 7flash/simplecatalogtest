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

class PluginSimplecatalog_HookOnlinecomments extends Hook {

	public function RegisterHook() {
		$this->AddHook('template_block_stream_nav_item', 'BlockStreamNavItem');
	}


	/**
	 * Добавление пунктов в меню-переключатель блока "прямой эфир"
	 *
	 * @return bool
	 */
	public function BlockStreamNavItem() {
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		$aSchemesAll = $this->PluginSimplecatalog_Scheme_MyGetSchemeItemsByActiveAndAllowCommentsInAndShowOnlineComments(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			array(
				PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_FORCED_TO_ALLOW,
				PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_USER_DEFINED
			),
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED
		);
		$this->Viewer_Assign('aSchemesWithAllowedComments', $aSchemesAll);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'blocks/block_stream_nav_item.tpl');
	}

}

?>