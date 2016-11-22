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

class PluginSimplecatalog_ModuleSearch_MapperSearch extends Mapper {


	/**
	 * Найти продукты по проверенному запросу
	 *
	 * @param       $iModeration         тип модерации
	 * @param       $iSchemeId           ид схемы
	 * @param       $sQuery              запрос
	 * @param       $iCurrentPage        страница
	 * @param       $iPerPage            количество на страницу
	 * @param array $aSortOrder          орм сортировка
	 * @return mixed            продукты
	 */
	public function GetProductsByModerationAndSchemeIdAndContentLike($iModeration, $iSchemeId, $sQuery, $iCurrentPage, $iPerPage, $aSortOrder) {
		$aSortOrderKeys = array_keys($aSortOrder);
		$aSortOrderValues = array_values($aSortOrder);

		$sSql = 'SELECT DISTINCT p.`id`
			FROM
				`' . Config::Get('db.table.simplecatalog_product') . '` as p,
				`' . Config::Get('db.table.simplecatalog_product_fields') . '` as f
			WHERE
				p.`id` = f.`product_id`
			AND
				p.`moderation` = ?d
			AND
				p.`scheme_id` = ?d
			AND
				(
					(f.`content_int` LIKE ? AND f.`content_type` = ?d)
					OR
					(f.`content_float` LIKE ? AND f.`content_type` = ?d)
					OR
					(f.`content_varchar` LIKE ? AND f.`content_type` = ?d)
					OR
					(f.`content_text` LIKE ? AND f.`content_type` = ?d)
				)
			ORDER BY
				p.`' . reset($aSortOrderKeys) . '` ' . reset($aSortOrderValues) . '
			LIMIT ?d, ?d
		';
		/*
		 * всего найденных записей
		 */
		$iTotalCount = 0;
		$aItems = array();
		
		if ($aResult = $this->oDb->selectPage(
			$iTotalCount,
			$sSql,
			$iModeration,
			$iSchemeId,

			$sQuery,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_INT,
			$sQuery,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_FLOAT,
			$sQuery,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_VARCHAR,
			$sQuery,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_TEXT,

			($iCurrentPage - 1) * $iPerPage,
			$iPerPage
		)) {
			/*
			 * получить все продукты за один запрос
			 */
			$aItems = Engine::getInstance()->PluginSimplecatalog_Product_MyGetProductItemsByFilter(array(
				'id IN' => my_array_column($aResult, 'id'),
				'#order' => $aSortOrder
			));
		}
		return array(
			'collection' => $aItems,
			'count' => $iTotalCount
		);
	}

}
