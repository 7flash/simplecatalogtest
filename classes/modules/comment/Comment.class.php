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

class PluginSimplecatalog_ModuleComment extends PluginSimplecatalog_Inherits_ModuleComment {

	/**
	 * Перенести комментарии из прямого эфира со старого типа цели на новый
	 *
	 * @param $sTargetTypeOld
	 * @param $sTargetTypeNew
	 * @return mixed
	 */
	public function TransferOnlineCommentsTargetTypeFromOldToNew($sTargetTypeOld, $sTargetTypeNew) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('comment_online_update_' . $sTargetTypeOld));
		return $this->oMapper->TransferOnlineCommentsTargetTypeFromOldToNew($sTargetTypeOld, $sTargetTypeNew);
	}


	/**
	 * Удалить комментарии из прямого эфира по типу цели
	 *
	 * @param $sTargetType
	 * @return mixed
	 */
	public function DeleteOnlineCommentsByTargetType($sTargetType) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('comment_online_update_' . $sTargetType));
		return $this->oMapper->DeleteOnlineCommentsByTargetType($sTargetType);
	}


	/**
	 * Получить тип цели для комментариев "прямого эфира" продуктов схемы по её урлу
	 *
	 * @param $sUrl			урл схемы
	 * @return string
	 */
	public function GetSchemeOnlineCommentsTargetTypeByUrl($sUrl) {
		return PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT . '_' . $sUrl;
	}

}

?>