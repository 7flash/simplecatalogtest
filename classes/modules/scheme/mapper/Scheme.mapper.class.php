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

class PluginSimplecatalog_ModuleScheme_MapperScheme extends Mapper {

	/**
	 * Получить максимальное значение сортировки для поля схемы
	 *
	 * @param $iSchemeId	ид схемы
	 * @return int			макс. текущее значение
	 */
	public function GetMaxSortingValueForSchemeField($iSchemeId) {
		$sSql = 'SELECT MAX(`sorting`)
			FROM
				`' . Config::Get('db.table.simplecatalog_scheme_fields') . '`
			WHERE
				`scheme_id` = ?d
		';
		return (int) $this->oDb->selectCell($sSql, $iSchemeId);
	}


	/**
	 * Получить максимальное значение сортировки схем
	 *
	 * @return int			макс. текущее значение
	 */
	public function GetMaxSortingValueForScheme() {
		$sSql = 'SELECT MAX(`sorting`)
			FROM
				`' . Config::Get('db.table.simplecatalog_scheme') . '`
		';
		return (int) $this->oDb->selectCell($sSql);
	}

}

?>