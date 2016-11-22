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

/*
 *
 * Модуль для работы с метками на карте
 *
 */

class PluginSimplecatalog_ModuleMaps extends ModuleORM {

	/*
	 * Тип цели - продукт
	 */
	const TARGET_TYPE_PRODUCTS = 1;


	/**
	 * Добавить к продукту метки на карте по сырым данным из массива
	 *
	 * @param $oScheme					схема продукта
	 * @param $oProduct					продукт, для которого будут заданы метки
	 * @param $aRawPostData				массив с данными
	 */
	public function AddMapItemsToProductByRawPostData($oScheme, $oProduct, $aRawPostData) {
		/*
		 * удалить метки, которых больше нет
		 * tip: не менять порядок (выполнять перед добавлением) т.к. будут удаляться только что созданные метки т.к. у них нет ид в реквесте
		 */
		$this->DeleteRemovedMapItems($oProduct, $aRawPostData);
		/*
		 * добавить или обновить метки
		 */
		$this->UpdateMapItems($oScheme, $oProduct, $aRawPostData);
	}


	/**
	 * Добавить или обновить метки на карте по сырым данным из массива
	 *
	 * @param $oScheme					схема продукта
	 * @param $oProduct					продукт, для которого будут заданы метки
	 * @param $aRawPostData				массив с данными
	 */
	protected function UpdateMapItems($oScheme, $oProduct, $aRawPostData) {
		/*
		 * за основу для цикла взять первый и точно заданный параметр - широту
		 */
		$aCoordsLatRaw = (array) @$aRawPostData['coord_lat'];
		/*
		 * макс. количество разрешенных точек на карте
		 */
		$iAllowedItemsCount = $oScheme->getMapItemsMax();
		/*
		 * текущее количество точек
		 */
		$iItemsCurrent = 0;

		foreach(array_keys($aCoordsLatRaw) as $iKey) {
			$oEnt = Engine::GetEntity('PluginSimplecatalog_Maps_Item');

			$oEnt->setId(@$aRawPostData['id'][$iKey]);

			$oEnt->setTargetType(self::TARGET_TYPE_PRODUCTS);
			$oEnt->setTargetId($oProduct->getId());

			$oEnt->setLat(@$aRawPostData['coord_lat'][$iKey]);
			$oEnt->setLng(@$aRawPostData['coord_lng'][$iKey]);

			$oEnt->setTitle(strip_tags(@$aRawPostData['name'][$iKey]));
			$oEnt->setDescription($this->Text_Parser(@$aRawPostData['content'][$iKey]));

			$oEnt->setExtraHint(strip_tags(@$aRawPostData['hint'][$iKey]));
			/*
			 * разрешено ли выбирать тип метки на карте
			 */
			if ($oScheme->getSelectPresetForMapItemsEnabled()) {
				$oEnt->setExtraPreset($this->Text_Parser(@$aRawPostData['preset'][$iKey]));
			}

			// for update process
			if ($oEnt->getId()) {
				$oEnt->_SetIsNew(false);
			}
			/*
			 * если валидация сущности не прошла - пропустить эту метку
			 */
			if (!$oEnt->_Validate()) {
				$this->Message_AddError($oEnt->_getValidateError(), null, true);
				continue;
			}

			/*
			 * проверить лимит точек на продукт
			 */
			$iItemsCurrent++;
			if ($iItemsCurrent > $iAllowedItemsCount) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.map_items.limit_exceeded', array('count' => $iAllowedItemsCount)), null, true);
				break;
			}

			$oEnt->Save();
		}
	}


	/**
	 * Удалить убранные метки из карты по сырым данным из массива
	 *
	 * @param $oProduct					продукт
	 * @param $aRawPostData				массив с данными
	 */
	protected function DeleteRemovedMapItems($oProduct, $aRawPostData) {
		/*
		 * текущие ид
		 */
		$aIdsOld = $oProduct->getMapItemsIds();
		/*
		 * ид тех, которые были присланы с формы, кроме пустых ид
		 */
		$aIdsNew = array_filter((array) @$aRawPostData['id']);

		/*
		 * ид для удаления
		 */
		if ($aIdsNeedToDelete = array_diff($aIdsOld, $aIdsNew)) {
			foreach($this->MyGetItemItemsByIdIn($aIdsNeedToDelete) as $oItem) {
				$oItem->Delete();
			}
		}
	}


	/**
	 * Получить массив с данными меток карты для редактирования продукта
	 *
	 * @param $oProduct					продукт
	 * @return array
	 */
	public function GetProductMapItemsForEditing($oProduct) {
		$aData = array();
		foreach($oProduct->getMapItems() as $oItem) {
			$aData['id'][] = $oItem->getId();
			$aData['coord_lat'][] = $oItem->getLat();
			$aData['coord_lng'][] = $oItem->getLng();
			$aData['name'][] = $oItem->getTitle();
			$aData['hint'][] = $oItem->getExtraHint();
			$aData['content'][] = $oItem->getDescription();
			$aData['preset'][] = $oItem->getExtraPreset();
		}
		return $aData;
	}


	/*
	 *
	 * --- Методы ORM ---
	 *
	 */

	/**
	 * Получить метки по типу и ид цели
	 *
	 * @param $iTargetType			тип цели
	 * @param $iTargetId			ид цели
	 * @return mixed
	 */
	public function MyGetItemItemsByTargetTypeAndTargetId($iTargetType, $iTargetId) {
		return $this->GetItemItemsByTargetTypeAndTargetId($iTargetType, $iTargetId);
	}


	/**
	 * Получить метки по массиву ид
	 *
	 * @param $aIds					массив ид меток
	 * @return mixed
	 */
	public function MyGetItemItemsByIdIn($aIds) {
		return $this->GetItemItemsByIdIn($aIds);
	}


	/*
	 *
	 * --- Для типа цели "продукт" ---
	 *
	 */

	/**
	 * Получить метки на карте для продукта
	 *
	 * @param $oProduct				продукт
	 * @return mixed
	 */
	public function MyGetProductMapItemsByProduct($oProduct) {
		return $this->MyGetItemItemsByTargetTypeAndTargetId(self::TARGET_TYPE_PRODUCTS, $oProduct->getId());
	}


	/**
	 * Получить количество элементов для схемы
	 *
	 * @param $oScheme				схема
	 * @return int
	 */
	public function MyGetSchemeMapItemsCountByScheme($oScheme) {
		return $this->PluginSimplecatalog_Myorm_CountByJoin(
			array(
				PluginSimplecatalog_ModuleMyorm::TABLE_B => Config::Get('db.table.simplecatalog_product'),
				PluginSimplecatalog_ModuleMyorm::ON_A_KEY => 'target_id',
				PluginSimplecatalog_ModuleMyorm::ON_B_KEY => 'id',

				'#where' => array(
					'b.`scheme_id` = ?d AND b.`moderation` IN (?a)' => array(
						$oScheme->getId(),
						array(PluginSimplecatalog_ModuleProduct::MODERATION_DONE),
					)
				),
				'target_type' => self::TARGET_TYPE_PRODUCTS,
			),
			array($this, 'Item')
		);
	}


	/**
	 * Получить все метки по фильтру, схеме и пройденной модерации продуктов
	 *
	 * @param $aFilter				фильтр
	 * @param $oScheme				схема
	 * @return entity|null
	 */
	public function MyGetItemsByFilterAndSchemeAndProductModerationDone($aFilter, $oScheme) {
		return $this->PluginSimplecatalog_Myorm_GetItemsByJoin(
			array(
				PluginSimplecatalog_ModuleMyorm::TABLE_B => Config::Get('db.table.simplecatalog_product'),
				PluginSimplecatalog_ModuleMyorm::ON_A_KEY => 'target_id',
				PluginSimplecatalog_ModuleMyorm::ON_B_KEY => 'id',

				'#where' => array(
					'b.`scheme_id` = ?d AND b.`moderation` IN (?a)' => array(
						$oScheme->getId(),
						array(PluginSimplecatalog_ModuleProduct::MODERATION_DONE),
					)
				),
				'target_type' => self::TARGET_TYPE_PRODUCTS,
			) + $aFilter,
			array($this, 'Item')
		);
	}


}

?>