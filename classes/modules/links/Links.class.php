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
 * --- Модуль работы со связями ---
 *
 */

class PluginSimplecatalog_ModuleLinks extends ModuleORM {

	/*
	 * Типы связи
	 */
	const LINK_TYPE_HAS_ONE = 1;
	const LINK_TYPE_HAS_MANY = 2;

	/*
	 * Формат вывода
	 */
	const DISPLAY_LINK_TYPE_IN_TAB = 1;
	const DISPLAY_LINK_TYPE_AS_LINKS = 2;
	const DISPLAY_LINK_TYPE_AS_IMAGES = 4;
	const DISPLAY_LINK_TYPE_IN_SELECT = 8;

	/*
	 * Выбор продуктов
	 */
	const SELECT_LINK_TYPE_ALL = 1;
	const SELECT_LINK_TYPE_SELF = 2;

	/*
	 * Тип привязки
	 */
	const TARGET_TYPE_PRODUCTS = 1;

	/*
	 * Тип родительской конфигурации связей
	 */
	const PARENT_TYPE_SCHEME_LINKS_SETTINGS = 1;


	/*
	 *
	 * --- Обертки для ORM ---
	 *
	 */

	/**
	 * Получить связанные объекты по типу и ид родителя, по типу и ид цели и типу конечной привязки
	 *
	 * @param 			$iParentType					тип родителя
	 * @param 			$iParentId						ид родителя
	 * @param 			$iFromTargetType				тип цели
	 * @param 			$iFromTargetId					ид цели
	 * @param 			$iToTargetType					тип конечной привязки
	 * @param array 	$aParams						параметры
	 * @return mixed
	 */
	public function MyGetLinkItemsByParentTypeAndParentIdAndFromTargetTypeAndFromTargetIdAndToTargetType($iParentType, $iParentId, $iFromTargetType, $iFromTargetId, $iToTargetType, $aParams = array()) {
		return $this->GetLinkItemsByParentTypeAndParentIdAndFromTargetTypeAndFromTargetIdAndToTargetType($iParentType, $iParentId, $iFromTargetType, $iFromTargetId, $iToTargetType, $aParams);
	}


	/**
	 * Получить связи продукта с другими продуктами по настройкам связи схемы
	 *
	 * @param 			$oLinkSettings					настройки связи схемы
	 * @param 			$oProduct						продукт
	 * @param array 	$aParams						параметры
	 * @return mixed
	 */
	public function MyGetProductLinkItemsByParentSchemeLinkSettingsAndProduct($oLinkSettings, $oProduct, $aParams = array()) {
		return $this->MyGetLinkItemsByParentTypeAndParentIdAndFromTargetTypeAndFromTargetIdAndToTargetType(
			self::PARENT_TYPE_SCHEME_LINKS_SETTINGS,
			$oLinkSettings->getId(),
			self::TARGET_TYPE_PRODUCTS,
			$oProduct->getId(),
			self::TARGET_TYPE_PRODUCTS,
			$aParams
		);
	}


	/**
	 * Получить связанные объекты по типу и ид родителя
	 *
	 * @param 			$iParentType					тип родителя
	 * @param 			$iParentId						ид родителя
	 * @return mixed
	 */
	public function MyGetLinkItemsByParentTypeAndParentId($iParentType, $iParentId) {
		return $this->GetLinkItemsByParentTypeAndParentId($iParentType, $iParentId);
	}


	/**
	 * Удалить все связи принадлежащей настройке связей схемы
	 *
	 * @param $oLinkSettings							сущность настройки связи схемы
	 */
	public function MyDeleteProductLinkItemsByParentSchemeLinkSettings($oLinkSettings) {
		if ($aLinks = $this->MyGetLinkItemsByParentTypeAndParentId(self::PARENT_TYPE_SCHEME_LINKS_SETTINGS, $oLinkSettings->getId())) {
			foreach($aLinks as $oLink) {
				$oLink->Delete();
			}
		}
	}


	/**
	 * Получить связанные объекты по типу и ид первичной цели (from)
	 *
	 * @param 			$iFromTargetType				тип первичной цели
	 * @param 			$iFromTargetId					ид первичной цели
	 * @return mixed
	 */
	public function MyGetLinkItemsByFromTargetTypeAndFromTargetId($iFromTargetType, $iFromTargetId) {
		return $this->GetLinkItemsByFromTargetTypeAndFromTargetId($iFromTargetType, $iFromTargetId);
	}


	/**
	 * Удалить все связи продукта, который значится как первичная цель (from)
	 *
	 * @param $oProduct									сущность продукта
	 */
	public function MyDeleteProductLinkItemsByFromTargetProduct($oProduct) {
		if ($aLinks = $this->MyGetLinkItemsByFromTargetTypeAndFromTargetId(self::TARGET_TYPE_PRODUCTS, $oProduct->getId())) {
			foreach($aLinks as $oLink) {
				$oLink->Delete();
			}
		}
	}


	/**
	 * Получить связанные объекты по типу и ид связанной цели (to)
	 *
	 * @param 			$iToTargetType					тип связанной цели
	 * @param 			$iToTargetId					ид связанной цели
	 * @return mixed
	 */
	public function MyGetLinkItemsByToTargetTypeAndToTargetId($iToTargetType, $iToTargetId) {
		return $this->GetLinkItemsByToTargetTypeAndToTargetId($iToTargetType, $iToTargetId);
	}


	/**
	 * Удалить все связи продукта, который значится как связанная цель (to)
	 *
	 * @param $oProduct									сущность продукта
	 */
	public function MyDeleteProductLinkItemsByToTargetProduct($oProduct) {
		if ($aLinks = $this->MyGetLinkItemsByToTargetTypeAndToTargetId(self::TARGET_TYPE_PRODUCTS, $oProduct->getId())) {
			foreach($aLinks as $oLink) {
				$oLink->Delete();
			}
		}
	}


	/**
	 * Удалить все связи продукта (его и на него)
	 *
	 * @param $oProduct									сущность продукта
	 */
	public function MyDeleteAllProductLinkItemsByProduct($oProduct) {
		/*
		 * удаление связей продукта (где он значится в поле "from_target_id" и "from_target_type" указывает на продукт)
		 */
		$this->MyDeleteProductLinkItemsByFromTargetProduct($oProduct);
		/*
		 * удаление продукта из связей других продуктов (где он значится в поле "to_target_id" и "to_target_type" указывает на продукт)
		 */
		$this->MyDeleteProductLinkItemsByToTargetProduct($oProduct);
	}

}

?>