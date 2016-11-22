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
 * Для сортировки сущностей по полю sorting таблицы
 *
 */

class PluginSimplecatalog_ModuleSorting extends Module {


	public function Init() {}


	/**
	 * Установить значения сортировки для массива сущностей согласно порядку "сырых" ид из другого массива
	 *
	 * @param      $aItems                          массив сущностей, в котором ключи - ид сущностей
	 * @param      $aItemsIdsRaw                    массив "сырых" ид сущностей в порядке для установки сортировки
	 * @param bool $bReverseSortingOrder            использовать установку значений от большего к меньшему (иначе - от меньшего к большему)
	 */
	public function SortItemsByRawIds($aItems, $aItemsIdsRaw, $bReverseSortingOrder = true) {
		/*
		 * сбросить ключи к числовым для удобства установки номера сортировки
		 */
		$aItemsIdsRaw = array_values($aItemsIdsRaw);
		/*
		 * перевернуть массив если сортировка идет по спаданию (сортировка desc)
		 */
		if ($bReverseSortingOrder) {
			$aItemsIdsRaw = array_reverse($aItemsIdsRaw);
		}
		/*
		 * в порядке указания сырых ид сущностей задать новые значения сортировки
		 */
		foreach ($aItemsIdsRaw as $iKey => $iIdRaw) {
			/*
			 * существует ли сущность по такому "сырому" ид
			 */
			if (isset($aItems[$iIdRaw])) {
				/*
				 * установить новое значение сортировки (двигаясь от младшего значения к старшему)
				 * tip: "+ 1" нужен чтобы нумерация не начиналась с нуля, т.к. тогда автоматически будет подобран свободный номер в beforeSave сущности
				 */
				$aItems[$iIdRaw]->setSorting($iKey + 1);
				$aItems[$iIdRaw]->Save();
			}
		}
	}


	/**
	 * Получить булевое значение необходимости реверса установки сортировки для метода SortItemsByRawIds на основе массива сортировки для орм
	 *
	 * @param $aOrder		массив сортировки для орм формата array('field' => 'desc')
	 * @return bool			нужна ли реверсная сортировка
	 */
	private function GetBoolReverseSortingOrderByORMSortingOrderArray($aOrder) {
		$aOrderValues = array_values($aOrder);
		return array_shift($aOrderValues) == 'desc';
	}


	/**
	 * Установить значения сортировки для массива сущностей согласно порядку "сырых" ид из другого массива и массива сортировки орм
	 *
	 * @param $aItems						массив сущностей, в котором ключи - ид сущностей
	 * @param $aItemsIdsRaw					массив "сырых" ид сущностей в порядке для установки сортировки
	 * @param $aOrder						массив сортировки для орм формата array('field' => 'desc')
	 */
	public function SortItemsByRawIdsAndORMSortOrderArray($aItems, $aItemsIdsRaw, $aOrder) {
		$this->SortItemsByRawIds($aItems, $aItemsIdsRaw, $this->GetBoolReverseSortingOrderByORMSortingOrderArray($aOrder));
	}

}

?>