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

class PluginSimplecatalog_ModuleCategory_MapperCategory extends Mapper {

	/**
	 * Получить максимальное значение сортировки для типа источника
	 *
	 * @param $iTargetType		тип источника
	 * @return int				макс. текущее значение
	 */
	public function GetMaxSortingValue($iTargetType) {
		$sSql = 'SELECT MAX(`sorting`)
			FROM
				`' . Config::Get('db.table.simplecatalog_category') . '`
			WHERE
				`target_type` = ?d
		';
		return (int) $this->oDb->selectCell($sSql, $iTargetType);
	}

}

?>