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

class PluginSimplecatalog_ModuleProduct_MapperProduct extends Mapper {

	/**
	 * Отвязать основные картинки от проекта:
	 *      Удалить картинки проекта из таблицы images и проапдейтить свойства проекта
	 * @param array(project_id=>1)
	 * @return array
	 */
	public function RemoveProjectImages($arParams) {

		if (!isset($arParams["project_id"])
			|| !isset($arParams["image_detail_id"]) || !isset($arParams["image_preview_id"])
		) {
			return array("res" => "fail");
		}

		$this->oDb->transaction();

		$sql_delete_images = '
			DELETE FROM `' . Config::Get ('db.table.simplecatalog_images_image') . '`
			WHERE `id` IN (?d,?d) ';

		$res_delete_images = $this->oDb->query($sql_delete_images, $arParams["image_detail_id"], $arParams["image_preview_id"]);

		if (!$res_delete_images) {
			$this->oDb->rollback();
			return array("res" => "fail");
		}

		$sql_update_product = '
			UPDATE `' . Config::Get('db.table.simplecatalog_product') . '`
			SET `image_detail`=0, `image_preview`=0  WHERE `id`=?d ';

		$res_update_product = $this->oDb->query($sql_update_product, $arParams["project_id"]);

		if (!$res_update_product) {
			$this->oDb->rollback();
			return array("res" => "fail");
		}

		$this->oDb->commit();

		return array("res" => "ok");
	}

	/**
	 * Получить уникальные все первые буквы полей продуктов по ид поля схемы и с указанным типом модерации продукта (для групп букв поиска)
	 *
	 * @param $iFieldId			ид поля (у схемы)
	 * @param $iModeration		тип модерации
	 * @param $aContentTypes	типы полей для контента
	 * @return array
	 */
	public function GetFirstLetterGroupsRawBySchemeFieldIdAndJoinProductModeration($iFieldId, $iModeration, $aContentTypes) {
		$sSql = 'SELECT DISTINCT
			CASE
				WHEN (`content_type` = ?d) THEN (UPPER(LEFT(?#, 1)))
				WHEN (`content_type` = ?d) THEN (UPPER(LEFT(?#, 1)))
				WHEN (`content_type` = ?d) THEN (UPPER(LEFT(?#, 1)))
				WHEN (`content_type` = ?d) THEN (UPPER(LEFT(?#, 1)))
			END AS firstletter
			FROM
				`' . Config::Get('db.table.simplecatalog_product') . '` as p,
				`' . Config::Get('db.table.simplecatalog_product_fields') . '` as pf
			WHERE
				p.`id` = pf.`product_id`
				AND
				pf.`field_id` = ?d
				AND
				p.`moderation` = ?d
			HAVING
				firstletter <> ""
			ORDER BY
				firstletter
		';
		if ($aResult = $this->oDb->selectCol($sSql,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_INT,
			$aContentTypes[PluginSimplecatalog_ModuleProduct::FIELD_TYPE_INT],

			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_FLOAT,
			$aContentTypes[PluginSimplecatalog_ModuleProduct::FIELD_TYPE_FLOAT],

			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_VARCHAR,
			$aContentTypes[PluginSimplecatalog_ModuleProduct::FIELD_TYPE_VARCHAR],

			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_TEXT,
			$aContentTypes[PluginSimplecatalog_ModuleProduct::FIELD_TYPE_TEXT],

			$iFieldId,
			$iModeration
		)) {
			return $aResult;
		}
		return array();
	}


	/**
	 * Добавить запись нового поля со значением по-умолчанию для каждого продукта из списка
	 *
	 * @param     $aProductsIds        ид продуктов для добавления
	 * @param     $oField              объект поля схемы
	 * @param int $iContentType        тип контента
	 * @param     $aContentTypes       массив типов контента
	 * @return array|null
	 */
	public function AddDefaultFieldValueForListedProducts($aProductsIds, $oField, $iContentType, $aContentTypes) {
		/*
		 * список значений для вставки
		 */
		$sSqlInsertValues = $this->BuildSqlForMultipleInsertingDefaultFieldValueForListedProducts($aProductsIds, $oField->getId(), $oField->getDefaultValue(), $iContentType);
		/*
		 * получить первое поле, в которое будет записано значение
		 */
		$sFirstDataField = $aContentTypes[$iContentType];
		/*
		 * удалить это поле, чтобы потом просто вывести остальные поля
		 */
		unset($aContentTypes[$iContentType]);
		$sSql = 'INSERT INTO
				`' . Config::Get('db.table.simplecatalog_product_fields') . '`
			(
				`product_id`,
				`field_id`,

				`content_type`,

				`' . $sFirstDataField . '`,
				`' . implode('`, `', $aContentTypes) . '`,
				`content_source`
			)
			VALUES
				' . $sSqlInsertValues;
		return $this->oDb->query($sSql);
	}


	/**
	 * Построить часть запроса на вставку нового поля со значением по-умолчанию для каждого существующего продукта
	 *
	 * @param $aProductsIds		ид продуктов
	 * @param $iFieldId			ид нового поля
	 * @param $mDefaultValue	вставляемое значение (для всех полей)
	 * @param $iContentType		тип контента
	 * @return string			часть скл запроса вставки
	 */
	protected function BuildSqlForMultipleInsertingDefaultFieldValueForListedProducts($aProductsIds, $iFieldId, $mDefaultValue, $iContentType) {
		$sSql = '';
		/*
		 * экранирование значения по-умолчанию
		 */
		$mValue = $this->oDb->escape($mDefaultValue);
		/*
		 * для каждого продукта
		 */
		foreach($aProductsIds as $iProductId) {
			/*
			 * вставка ид продукта, ид поля схемы, типа поля и значения в два столбца: по типу контента и поле исходных вводимых данных
			 * (поле для значения по типу контента в запросе будет первым, остальные будут заполнены пустыми значениями)
			 */
			$sSql .= '(' . $iProductId . ', ' . $iFieldId . ', ' . $iContentType . ', ' . $mValue . ', "", "", "", ' . $mValue . '),';
		}
		$sSql = rtrim($sSql, ',');
		return $sSql;
	}


	/**
	 * Выполнить процесс миграции контента из одного поля таблицы полей продукта в другое по полю схемы, тип которого был изменен
	 *
	 * @param Entity $oField          объект поля схемы
	 * @param int    $iOldContentType старый тип контента
	 * @param int    $iNewContentType новый тип контента
	 * @param array  $aContentTypes   массив типов контентов, указывающих на соответствующие поля таблицы
	 * @return array|null
	 */
	public function PerformDataMigrationFromOneTableFieldToAnother($oField, $iOldContentType, $iNewContentType, $aContentTypes) {
		$sSql = 'UPDATE
				`' . Config::Get('db.table.simplecatalog_product_fields') . '`
			SET
				`' . $aContentTypes[$iNewContentType] . '` = `' . $aContentTypes[$iOldContentType] . '`,
				`' . $aContentTypes[$iOldContentType] . '` = "",
				`content_type` = ?d
			WHERE
				`field_id` = ?d
		';
		return $this->oDb->query($sSql, $iNewContentType, $oField->getId());
	}


	/**
	 * Получить данные с помощью агрегирующей функции по указанному полю таблицы поля схемы промодерированных продуктов
	 *
	 * @param $sAggrFunction	имя агрегирующей mysql функции
	 * @param $sFieldName		имя поля таблицы для функции
	 * @param $oField			объект поля схемы
	 * @param $iModeration		тип модерации продуктов
	 * @return int|null
	 */
	public function GetAggregateDataFromProductFields($sAggrFunction, $sFieldName, $oField, $iModeration) {
		$sWhatToSelect = $sAggrFunction . '(' . $this->oDb->escape($sFieldName, true) . ')';

		$sSql = 'SELECT
				' . $sWhatToSelect . '
			FROM
				?# as p,
				?# as pf
			WHERE
				p.`id` = pf.`product_id`
				AND
				pf.`field_id` = ?d
				AND
				p.`moderation` = ?d
		';
		if ($mData = $this->oDb->selectRow($sSql,
			Config::Get('db.table.simplecatalog_product'),
			Config::Get('db.table.simplecatalog_product_fields'),

			$oField->getId(),
			$iModeration
		)) {
			return $mData[$sWhatToSelect];
		}
		return null;
	}


	/*
	 *
	 * --- Получение продуктов по условиям полей фильтра ---
	 *
	 */

	/**
	 * Получить продукты и общее количество по правилам полей фильтра продуктов
	 *
	 * @param $aWhereConditions		массив правил для построения условия WHERE подзапроса
	 * @param $aCategoriesIds		массив ид категорий
	 * @param $iModeration			тип модерации
	 * @param $oScheme				объект схемы
	 * @param $iPage				страница
	 * @param $iPerPage				количество на странице
	 * @param $aSortOrder			орм массив сортировки и её направления
	 * @return array
	 */
	public function GetProductItemsByProductFilterFields($aWhereConditions, $aCategoriesIds, $iModeration, $oScheme, $iPage, $iPerPage, $aSortOrder) {
		/*
		 * количество полей фильтра, в которых были заданы значения для поиска
		 *
		 * tip: для сравнения сколько полей вернули один и тот же ид продукта в запросе по полям с общим их количеством (для группировки)
		 * 		т.е. количество найденных ид продуктов при группировке по ид продуктов должно равняться общему количеству полей в условии фильтра
		 * 		это означает что все условия совпали
		 */
		$iFilterConditionsCount = count($aWhereConditions);

		/*
		 * массив ид продуктов из запроса по условиям полей фильтра
		 */
		$aProductIdsFromConditionsQuery = array();
		/*
		 * если есть хотя бы одно условие поиска по полям
		 */
		if ($iFilterConditionsCount) {
			/*
			 * построить строку условия поиска по полям в которых были значения
			 */
			$sSubWhere = $this->BuildWhereQueryForProductFilterByWhereConditions($aWhereConditions);

			$sSql = 'SELECT pf.`product_id`
				FROM
					?# as pf
				WHERE
					' . $sSubWhere . '
				GROUP BY
					pf.`product_id`
				HAVING
					-- check all selected fields are matched
					COUNT(pf.`product_id`) = ?d
			';
			if ($mSubResult = $this->oDb->select($sSql,
				Config::Get('db.table.simplecatalog_product_fields'),
				$iFilterConditionsCount
			)) {
				$aProductIdsFromConditionsQuery = my_array_column($mSubResult, 'product_id');
			}
		}


		/*
		 * для сортировки
		 */
		$aSortOrderKeys = array_keys($aSortOrder);
		$aSortOrderValues = array_values($aSortOrder);

		/*
		 * tip: ид схемы не указывается для продуктов т.к. ид полей схемы, которые указаны в запросе по полям - сквозные и однозначно идентифицируют схему своими ид
		 *
		 * Старое описание запроса, в котором раньше был субзапрос по полям:
		 * 		Запрос можно переделать наоборот - так чтобы в подзапросе была выборка продуктов по категориям, модерации и ид схемы (нужно будет добавить т.к. сейчас её нет),
		 * 		а потом искать среди полей в указанных ид продуктов. Такой тип запроса может иметь выигрыш в скорости если постоянно будут указывать категории продуктов
		 * 		и/или есть топики на модерации. Таким образом сложный поиск по полям будет произведен по меньшему диапазону полей (указанных продуктов).
		 * 		Но в обычной ситуации когда топиков на модерации очень мало или совсем нет, а категории не указываются, то разворот  запроса не имеет смысла.
		 * 		Также важно помнить что при развороте запроса из полей можно получить только ид продуктов
		 * 		т.е. потом после запроса нужен будет ещё один запрос на получения продуктов по массиву ид. А это ещё одно обращение к БД.
		 */
		$sSql = 'SELECT DISTINCT p.*
			FROM
				?# as p{,
				?# as pc}
			WHERE
				-- categories
				{p.`id` = pc.`product_id`
				AND
				pc.`category_id` IN (?a)
				AND}

				p.`moderation` = ?d

				-- ids from query of product fields filter
				{AND
				p.`id` IN (?a)}

				-- when no ids from query of fields filter (disabled) need to set scheme id to select products only from needed scheme
				{AND
				p.`scheme_id` = ?d}
			ORDER BY
				`' . array_shift($aSortOrderKeys) . '` ' . array_shift($aSortOrderValues) . '
			LIMIT ?d, ?d
		';
		$iTotalCount = 0;
		$aItems = array();

		/*
		 * если был запрос по полям и есть по нему результаты или если условия по полям не заданы
		 * иначе поиск по условиям полей не нашел ни одного ид продукта
		 */
		if (($iFilterConditionsCount and $aProductIdsFromConditionsQuery) or !$iFilterConditionsCount) {
			if ($mResult = $this->oDb->selectPage($iTotalCount, $sSql,
				Config::Get('db.table.simplecatalog_product'),
				/*
				 * были ли выбраны категории для поиска
				 */
				$aCategoriesIds ? Config::Get('db.table.simplecatalog_product_categories') : DBSIMPLE_SKIP,
				$aCategoriesIds ? $aCategoriesIds : DBSIMPLE_SKIP,
				/*
				 * условия таблицы продуктов
				 */
				$iModeration,
				/*
				 * если есть ид продуктов из запроса по полям фильтра
				 */
				$aProductIdsFromConditionsQuery ? $aProductIdsFromConditionsQuery : DBSIMPLE_SKIP,
				/*
				 * если нет ид продуктов из запроса по полям фильтра (не указано ни одно значение для поиска по полям), то нужно добавить условие поиска по ид схемы
				 * т.к. список ид продуктов идентифицировал схему (по ид полей по которым производился поиск, у них сквозные ид и они идентифицируют собой схему)
				 */
				!$aProductIdsFromConditionsQuery ? $oScheme->getId() : DBSIMPLE_SKIP,
				/*
				 * пагинация
				 */
				($iPage - 1) * $iPerPage,
				$iPerPage
			)) {
				foreach($mResult as $aRow) {
					$aItems[] = Engine::GetEntity('PluginSimplecatalog_Product', $aRow);
				}
			}
		}

		return array(
			'collection' => $aItems,
			'count' => $iTotalCount
		);
	}


	/**
	 * Построить строку WHERE для подзапроса по правилам построенным на основе данных полей из фильтра продуктов
	 *
	 * @param $aWhereConditions		правила полей фильтра продуктов
	 * @return string
	 * @throws Exception			при неизвестном типе операнда
	 */
	protected function BuildWhereQueryForProductFilterByWhereConditions($aWhereConditions) {
		/*
		 * ложное условие (для совпадения только других условий)
		 */
		$sWhere = '1 = 0';
		foreach($aWhereConditions as $aCondition) {
			/*
			 * для совпадения условий одного из полей; префикс таблицы полей продуктов
			 */
			$sWhere .= ' OR (
				pf.';
			/*
			 * в зависимости от типа операнда для сравнения
			 */
			switch ($aCondition['condition']['type']) {
				/*
				 * указание диапазона чисел
				 */
				case 'between':
					$sWhere .=
						$this->oDb->escape($aCondition['field_name'], true) . ' BETWEEN ' .
						$this->oDb->escape($aCondition['condition']['value'][0]) . ' AND ' . $this->oDb->escape($aCondition['condition']['value'][1]);
					break;
				/*
				 * поиск по части строки
				 */
				case 'regexp':
					$sWhere .= $this->oDb->escape($aCondition['field_name'], true) . ' REGEXP ' . $this->oDb->escape($aCondition['condition']['value'][0]);
					break;
				/*
				 * точное совпадение
				 */
				case 'equal':
					$sWhere .= $this->oDb->escape($aCondition['field_name'], true) . ' = ' . $this->oDb->escape($aCondition['condition']['value'][0]);
					break;
				/*
				 * неизвестное сравнение
				 */
				default:
					throw new Exception('SC: error: unknown condition type "' . $aCondition['condition']['type'] . '" in ' . __METHOD__);
			}
			/*
			 * добавление ид поля схемы
			 */
			$sWhere .= '
				AND
				pf.`field_id` = ' . $this->oDb->escape($aCondition['field_id']) . '
				)';
		}
		return $sWhere;
	}


}

?>