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

class PluginSimplecatalog_ModuleUserassign extends ModuleORM {

	/*
	 * Сортировка по-умолчанию
	 */
	protected $aDefaultSorting = array('group_id' => 'asc');

	/*
	 *
	 * --- Обертки для ORM методов ---
	 *
	 */
	
	/*
	 * --- Назначение прав пользователям ---
	 */

	/**
	 * Получить все назначения прав
	 *
	 * @return mixed
	 */
	public function MyGetUserassignItemsAll() {
		return $this->GetUserassignItemsAll(array('#order' => $this->aDefaultSorting));
	}


	/**
	 * Получить назначение по ид
	 *
	 * @param $iId		ид назначения
	 * @return mixed
	 */
	public function MyGetUserassignById($iId) {
		return $this->GetUserassignById($iId);
	}


	/**
	 * Получить все назначения по ид группы прав
	 *
	 * @param $iId		ид группы
	 * @return mixed
	 */
	public function MyGetUserassignItemsByGroupId($iId) {
		return $this->GetUserassignItemsByGroupId($iId, array('#order' => $this->aDefaultSorting));
	}

}

?>