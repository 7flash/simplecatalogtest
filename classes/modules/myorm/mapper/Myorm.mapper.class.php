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

class PluginSimplecatalog_ModuleMyorm_MapperMyorm extends MapperORM {


	/**
	 * Получить сущности по связанной таблице по фильтру
	 *
	 * @param $aFilter			фильтр
	 * @param $oEntitySample	пустая сущность для получения имен полей
	 * @return array
	 */
	public function GetItemsByJoin($aFilter, $oEntitySample) {
		/*
		 * получить значения полей и строку полей
		 */
		list($aFilterFields, $sFilterFields) = $this->BuildFilter($aFilter, $oEntitySample);
		/*
		 * получить сортировку и лимит
		 */
		list($sOrder, $sLimit) = $this->BuildFilterMore($aFilter, $oEntitySample);
		/*
		 * выбирать данные для сущности только из таблицы a
		 */
		$sSelect = isset($aFilter[PluginSimplecatalog_ModuleMyorm::SELECT]) ? $aFilter[PluginSimplecatalog_ModuleMyorm::SELECT] : 'DISTINCT a.*';
		$sSql = 'SELECT
				' . $sSelect . '
			FROM
				?# as a
			INNER JOIN
				?# as b
			ON
				a.?# = b.?#
			WHERE
				1 = 1
				' . $sFilterFields . '
				' . $sOrder . '
				' . $sLimit . '
		';
		/*
		 * всего найдено и массив найденных сущностей
		 */
		$iTotalCount = 0;
		$aItems = array();

		/*
		 * если указан ключ "#page", то нужно найти количество всех записей
		 */
		if (isset($aFilter['#page'])) {
			$aFirstParamValue = array(&$iTotalCount);
			$sDbMethod = 'selectPage';
		} elseif (isset($aFilter[PluginSimplecatalog_ModuleMyorm::SELECT])) {
			$aFirstParamValue = array();
			$sDbMethod = 'selectRow';
		} else {
			$aFirstParamValue = array();
			$sDbMethod = 'select';
		}

		/*
		 * весь массив передаваемых параметров в запрос
		 */
		$aParams = array_merge(
			/*
			 * количество записей
			 */
			$aFirstParamValue,
			/*
			 * запрос, таблицы и связь
			 */
			array(
				$sSql,
				$aFilter[PluginSimplecatalog_ModuleMyorm::TABLE_A],
				$aFilter[PluginSimplecatalog_ModuleMyorm::TABLE_B],
				$aFilter[PluginSimplecatalog_ModuleMyorm::ON_A_KEY],
				$aFilter[PluginSimplecatalog_ModuleMyorm::ON_B_KEY]
			),
			/*
			 * набор полей
			 */
			$aFilterFields
		);
		/*
		 * выполнить запрос нужным методом (считать или нет количество найденных записей всего)
		 */
		if ($aResult = call_user_func_array(array($this->oDb, $sDbMethod), $aParams)) {
			/*
			 * если не указан специфический запрос - получить сущности
			 */
			if (!isset($aFilter[PluginSimplecatalog_ModuleMyorm::SELECT])) {
				foreach($aResult as $aRow) {
					$oEnt = Engine::GetEntity($aFilter[PluginSimplecatalog_ModuleMyorm::ENTITY], $aRow);
					$oEnt->_SetIsNew(false);
					$aItems[] = $oEnt;
				}
			} else {
				$aItems = $aResult[$aFilter[PluginSimplecatalog_ModuleMyorm::SELECT]];
			}
		}

		/*
		 * если указан ключ "#page", то нужно изменить формат возвращаемых записей
		 */
		if (isset($aFilter['#page'])) {
			return array(
				'collection' => $aItems,
				'count' => $iTotalCount
			);
		}
		return $aItems;
	}

}

?>