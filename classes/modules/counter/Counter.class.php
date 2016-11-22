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
 * --- Модуль универсального счетчика ---
 *
 */

class PluginSimplecatalog_ModuleCounter extends ModuleORM {

	/*
	 * Типы цели
	 */
	const TARGET_TYPE_PRODUCT_VIEWS = 1;
	const TARGET_TYPE_PRODUCT_FIELD_FILE_DOWNLOADS = 2;


	/*
	 *
	 * --- Обертки для ORM ---
	 *
	 */

	/**
	 * Получить счетчик по типу и ид цели
	 *
	 * @param int $iTargetType тип цели
	 * @param int $iTargetId   ид цели
	 * @return Entity|null
	 */
	public function MyGetCounterByTargetTypeAndTargetId($iTargetType, $iTargetId) {
		return $this->GetCounterByTargetTypeAndTargetId($iTargetType, $iTargetId);
	}


	/**
	 * Увеличить счетчик по типу и ид цели
	 *
	 * @param int $iTargetType тип цели
	 * @param int $iTargetId   ид цели
	 * @param int $iStep       шаг увеличения счетчика
	 * @return bool|string     true или текст ошибки валидации
	 */
	public function IncreaseTargetCounter($iTargetType, $iTargetId, $iStep = 1) {
		if ($oCounter = $this->MyGetCounterByTargetTypeAndTargetId($iTargetType, $iTargetId)) {
			$oCounter->setCount($oCounter->getCount() + $iStep);
		} else {
			$oCounter = Engine::GetEntity('PluginSimplecatalog_Counter');
			$oCounter->setTargetType($iTargetType);
			$oCounter->setTargetId($iTargetId);
			$oCounter->setCount($iStep);
		}
		if (!$oCounter->_Validate()) {
			return $oCounter->_getValidateError();
		}
		$oCounter->Save();
		return true;
	}


	/**
	 * Удалить счетчик по типу и ид цели
	 *
	 * @param int $iTargetType тип цели
	 * @param int $iTargetId   ид цели
	 */
	public function DeleteCounterByTargetTypeAndTargetId($iTargetType, $iTargetId) {
		if ($oCounter = $this->MyGetCounterByTargetTypeAndTargetId($iTargetType, $iTargetId)) {
			$oCounter->Delete();
		}
	}


	/*
	 *
	 * --- Для просмотров продуктов ---
	 *
	 */

	/**
	 * Получить счетчик просмотров продукта
	 *
	 * @param $oProduct			сущность продукта
	 * @return Entity|null
	 */
	public function MyGetCounterByProduct($oProduct) {
		return $this->MyGetCounterByTargetTypeAndTargetId(self::TARGET_TYPE_PRODUCT_VIEWS, $oProduct->getId());
	}


	/**
	 * Увеличить счетчик просмотров для продукта
	 *
	 * @param     $oProduct		сущность продукта
	 * @param int $iStep		шаг увеличения счетчика
	 * @return bool|string		true или текст ошибки валидации
	 */
	public function IncreaseProductCounter($oProduct, $iStep = 1) {
		return $this->IncreaseTargetCounter(self::TARGET_TYPE_PRODUCT_VIEWS, $oProduct->getId(), $iStep);
	}


	/**
	 * Удалить счетчик просмотров продукта
	 *
	 * @param $oProduct			сущность продукта
	 */
	public function DeleteCounterByProduct($oProduct) {
		$this->DeleteCounterByTargetTypeAndTargetId(self::TARGET_TYPE_PRODUCT_VIEWS, $oProduct->getId());
	}


	/*
	 *
	 * --- Для количества загрузок файлов ---
	 *
	 */

	/**
	 * Получить счетчик количества загрузок файла по сущности поля продукта
	 *
	 * @param $oProductField		сущность поля продукта файла
	 * @return Entity|null
	 */
	public function MyGetCounterByProductField($oProductField) {
		return $this->MyGetCounterByTargetTypeAndTargetId(self::TARGET_TYPE_PRODUCT_FIELD_FILE_DOWNLOADS, $oProductField->getId());
	}


	/**
	 * Увеличить счетчик загрузок файла поля продукта
	 *
	 * @param     $oProductField    сущность поля продукта файла
	 * @param int $iStep            шаг увеличения счетчика
	 * @return bool|string        	true или текст ошибки валидации
	 */
	public function IncreaseProductFieldCounter($oProductField, $iStep = 1) {
		return $this->IncreaseTargetCounter(self::TARGET_TYPE_PRODUCT_FIELD_FILE_DOWNLOADS, $oProductField->getId(), $iStep);
	}


	/**
	 * Удалить счетчик загрузок файла поля продукта
	 *
	 * @param     $oProductField    сущность поля продукта файла
	 */
	public function DeleteProductFieldCounter($oProductField) {
		$this->DeleteCounterByTargetTypeAndTargetId(self::TARGET_TYPE_PRODUCT_FIELD_FILE_DOWNLOADS, $oProductField->getId());
	}

}

?>