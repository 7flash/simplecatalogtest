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

class PluginSimplecatalog_ModuleComment_MapperComment extends PluginSimplecatalog_Inherits_ModuleComment_MapperComment {

	/**
	 * Перенести комментарии из прямого эфира со старого типа цели на новый
	 *
	 * @param $sTargetTypeOld
	 * @param $sTargetTypeNew
	 * @return mixed
	 */
	public function TransferOnlineCommentsTargetTypeFromOldToNew($sTargetTypeOld, $sTargetTypeNew) {
		$sSql = 'UPDATE `' . Config::Get ('db.table.comment_online') . '`
			SET
				`target_type` = ?
			WHERE
				`target_type` = ?
		';
		if ($this->oDb->query($sSql, $sTargetTypeNew, $sTargetTypeOld)) {
			return true;
		}
		return false;
	}


	/**
	 * Удалить комментарии из прямого эфира по типу цели
	 *
	 * @param $sTargetType
	 * @return mixed
	 */
	public function DeleteOnlineCommentsByTargetType($sTargetType) {
		$sSql = 'DELETE
			FROM
				`' . Config::Get ('db.table.comment_online') . '`
			WHERE
				`target_type` = ?
		';
		if ($this->oDb->query($sSql, $sTargetType)) {
			return true;
		}
		return false;
	}

}

?>