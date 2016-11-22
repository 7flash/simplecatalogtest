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

class PluginSimplecatalog_ModuleUsergroup extends ModuleORM {

	/*
	 * Сортировка по-умолчанию
	 */
	protected $aDefaultSorting = array('scheme_id' => 'asc', 'active' => 'desc');

	/*
	 *
	 * --- Обертки ORM методов ---
	 *
	 */
	
	/*
	 * --- Группы прав пользователей ---
	 */

	/**
	 * Получить группу по ид
	 *
	 * @param $iId					ид группы
	 * @return mixed
	 */
	public function MyGetUsergroupById($iId) {
		return $this->GetUsergroupById($iId);
	}


	/**
	 * Получить активную группу по ид
	 *
	 * @param $iId					ид группы
	 * @return mixed
	 */
	public function MyGetActiveUsergroupById($iId) {
		return $this->GetUsergroupByIdAndActive($iId, PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED);
	}


	/**
	 * Получить все группы
	 *
	 * @return mixed
	 */
	public function MyGetUsergroupItemsAll() {
		return $this->GetUsergroupItemsAll(array('#order' => $this->aDefaultSorting));
	}


	/**
	 * Получить все активные группы
	 *
	 * @return mixed
	 */
	public function MyGetActiveUsergroupItems() {
		return $this->GetUsergroupItemsByActive(PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED, array('#order' => $this->aDefaultSorting));
	}


	/**
	 * Получить все группы по ид схемы
	 *
	 * @param $iId			ид схемы
	 * @return mixed
	 */
	public function MyGetUsergroupItemsBySchemeId($iId) {
		return $this->GetUsergroupItemsBySchemeId($iId);
	}


	/**
	 * Получить активную группу по ид пользователя привязки и ид схемы
	 *
	 * @param $iUserId				ид пользователя
	 * @param $iSchemeId			ид схемы
	 * @return bool|mixed
	 */
	public function MyGetActiveUsergroupByAssignUserIdAndSchemeId($iUserId, $iSchemeId) {
		return $this->PluginSimplecatalog_Myorm_GetByJoin(array(
			/*
			 * данные для кастомного myorm модуля
			 */
			PluginSimplecatalog_ModuleMyorm::TABLE_B => Config::Get('db.table.simplecatalog_userassign'),
			PluginSimplecatalog_ModuleMyorm::ON_B_KEY => 'group_id',
			/*
			 * условия орм лс
			 */
			'#where' => array(
				'a.`scheme_id` = ?d AND a.`active` = ?d AND b.`user_id` = ?d' => array($iSchemeId, PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED, $iUserId)
			),
		), array($this));
	}


}

?>