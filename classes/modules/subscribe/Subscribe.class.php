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

class PluginSimplecatalog_ModuleSubscribe extends PluginSimplecatalog_Inherits_ModuleSubscribe {


	/**
	 * Есть ли такой промодерированный продукт активной схемы
	 *
	 * @param $iTargetId		ид продукта
	 * @param $iStatus			статус
	 * @return bool
	 */
	public function CheckTargetProductNewComment($iTargetId, $iStatus) {
		return $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById($iTargetId) != null;
	}

}
