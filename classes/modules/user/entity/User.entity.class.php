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

class PluginSimplecatalog_ModuleUser_EntityUser extends PluginSimplecatalog_Inherits_ModuleUser_EntityUser {

	/*
	 * Кеширование группы прав для пользователя в разрезе схемы
	 */
	private $aUserGroupByScheme = array();


	/**
	 * Получить группу прав для пользователя и схемы
	 *
	 * @param $oScheme			сущность схемы
	 * @return bool
	 */
	private function GetUsergroupForThisUserByScheme($oScheme) {
		if (!isset($this->aUserGroupByScheme[$oScheme->getId()])) {
			/*
			 * tip: субмассив "data" добавлен чтобы isset выше возвращал true если запрос на группу уже был и вернул null (т.е. назначенной группы прав для пользователя нет)
			 */
			$this->aUserGroupByScheme[$oScheme->getId()]['data'] = $this->PluginSimplecatalog_Usergroup_MyGetActiveUsergroupByAssignUserIdAndSchemeId($this->getId(), $oScheme->getId());
		}
		return $this->aUserGroupByScheme[$oScheme->getId()]['data'];
	}


	/*
	 *
	 * --- Права пользователей (ACL) ---
	 *
	 */

	/**
	 * Имеет ли пользователь доступ к какому-либо разделу админки плагина
	 *
	 * @return bool
	 */
	public function getCanAccessToSimplecatalogToolbarButton() {
		return
			$this->isAdministrator() or
			$this->getCanUserManageSchemesOrIsAdmin() or
			$this->getCanUserManageUserGroupsOrIsAdmin() or
			$this->getCanUserManageUsersAssignToGroupsOrIsAdmin() or
			$this->getCanUserManageCategoriesOrIsAdmin() or
			$this->getCanUserManageOrdersOrIsAdmin() or
			$this->getCanUserManageLinksOrIsAdmin();
	}


	/*
	 * --- Схема ---
	 */

	/**
	 * Может ли пользователь редактировать схемы
	 *
	 * @return bool
	 */
	protected function getCanUserEditSchemes() {
		//return true;
		return false;
	}


	/**
	 * Может ли пользователь редактировать схемы или это админ
	 *
	 * @return bool
	 */
	public function getCanUserManageSchemesOrIsAdmin() {
		/*
		 * на будущее, сейчас только админы могут редактировать схемы
		 */
		if ($this->getCanUserEditSchemes() or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Поля схемы ---
	 */

	/**
	 * Может ли пользователь редактировать поля схемы
	 *
	 * @return bool
	 */
	protected function getCanUserEditSchemeFields() {
		return false;
	}


	/**
	 * Может ли пользователь редактировать поля схемы или это админ
	 *
	 * @return bool
	 */
	public function getCanUserEditSchemeFieldsOrIsAdmin() {
		/*
		 * на будущее, сейчас только админы могут редактировать поля схемы
		 */
		if ($this->getCanUserEditSchemeFields() or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Назначение прав пользователям ---
	 */

	/**
	 * Может ли пользователь назначать группы прав пользователям
	 *
	 * @return bool
	 */
	protected function getCanUserAssignUsersToGroups() {
		return false;
	}


	/**
	 * Может ли пользователь назначать группы прав пользователям или это админ
	 *
	 * @return bool
	 */
	public function getCanUserManageUsersAssignToGroupsOrIsAdmin() {
		/*
		 * на будущее, сейчас только админы могут назначать права
		 */
		if ($this->getCanUserAssignUsersToGroups() or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Группы прав ---
	 */

	/**
	 * Может ли пользователь управлять группами прав
	 *
	 * @return bool
	 */
	protected function getCanUserManageUserGroups() {
		return false;
	}


	/**
	 * Может ли пользователь управлять группами прав или это админ
	 *
	 * @return bool
	 */
	public function getCanUserManageUserGroupsOrIsAdmin() {
		/*
		 * на будущее, сейчас только админы могут редактировать группы прав
		 */
		if ($this->getCanUserManageUserGroups() or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Продукты ---
	 */

	/**
	 * Есть ли разрешение на создание НОВОГО продукта
	 *
	 * @param $oScheme		сущность схемы
	 * @return bool
	 */
	public function getCanAddNewProductsInScheme($oScheme) {
		/*
		 * если разрешено группой прав или это админ
		 */
		if ($this->getInGroupUserCanCreateNewProductsByScheme($oScheme) or $this->isAdministrator()) {
			return true;
		}
		/*
		 * если только админы могут создавать продукты
		 */
		if ($oScheme->getCanAddProducts() == PluginSimplecatalog_ModuleScheme::CAN_ADD_PRODUCTS_ADMINS) {
			return false;
		}
		/*
		 * минимальный рейтинг
		 */
		if ($this->getRating() < $oScheme->getMinUserRatingToCreateProducts()) {
			return false;
		}
		return true;
	}


	/**
	 * Есть ли группа прав в которой разрешено создание новых продуктов пользователем
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	protected function getInGroupUserCanCreateNewProductsByScheme($oScheme) {
		/*
		 * проверить настройки группы прав (если есть)
		 */
		if ($oUserGroup = $this->GetUsergroupForThisUserByScheme($oScheme) and $oUserGroup->getUserCanCreateNewProductsEnabled()) {
			return true;
		}
		return false;
	}


	/**
	 * Разрешение на РЕДАКТИРОВАНИЕ и УДАЛЕНИЕ продукта автору, админам или если у пользователя есть на это специальные права назначенные группой прав (модераторы схемы)
	 *
	 * @param $oProduct		сущность продукта
	 * @return bool
	 */
	public function getCanManageProduct($oProduct) {
		return $this->getCanUserEditProductByProduct($oProduct) or $this->getInGroupCanEditProductsBySchemeOrIsAdmin($oProduct->getScheme());
	}


	/**
	 * Есть ли группа прав в которой разрешено редактирование продуктов пользователем
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	protected function getInGroupCanEditProductsByScheme($oScheme) {
		/*
		 * проверить настройки группы прав (если есть)
		 */
		if ($oUserGroup = $this->GetUsergroupForThisUserByScheme($oScheme) and $oUserGroup->getCanUserEditProductsEnabled()) {
			return true;
		}
		return false;
	}


	/**
	 * Есть ли группа прав в которой разрешено редактирование продуктов пользователем или это админ
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	protected function getInGroupCanEditProductsBySchemeOrIsAdmin($oScheme) {
		if ($this->getInGroupCanEditProductsByScheme($oScheme) or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/**
	 * Является ли текущий пользователь автором продукта с возможностью редактирования
	 *
	 * @param $oProduct					сущность продукта
	 * @return bool
	 */
	protected function getCanUserEditProductByProduct($oProduct) {
		/*
		 * если это автор продукта
		 */
		if ($oProduct->getUserId() == $this->getId()) {
			$sDate = $oProduct->getEditDate();
			/*
			 * если последним редактировал продукт не его автор (например, модератор), то сравнивать дату создания
			 */
			if ($oProduct->getUserIdEditLast() != $oProduct->getUserId()) {
				$sDate = $oProduct->getAddDate();
			}
			$oScheme = $oProduct->getScheme();
			/*
			 * проверить количество дней, прошедших с момента последнего редактирования автором продукта
			 */
			if (!$oScheme->getDaysAuthorCanManageProductsAfterLastEditing() or (strtotime($sDate) >= strtotime('-' . $oScheme->getDaysAuthorCanManageProductsAfterLastEditing() . ' day'))) {
				return true;
			}
		}
		return false;
	}


	/*
	 * --- Нужна ли модерация ---
	 */

	/**
	 * Нужна ли модерация для продуктов текущего пользователя указанной схемы
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	protected function getUserProductsNeedModerationByScheme($oScheme) {
		/*
		 * если есть назначение группы прав, которая указывает на необходимость модерации
		 */
		if ($oUserGroup = $this->GetUsergroupForThisUserByScheme($oScheme) and $oUserGroup->getUserProductsNeedModerationEnabled()) {
			return true;
		}
		/*
		 * по-умолчанию - получить значение необходимости модерации из схемы
		 */
		if ($oScheme->getModerationNeededEnabled()) {
			return true;
		}
		return false;
	}


	/**
	 * Нужна ли модерация для продуктов текущего пользователя указанной схемы и это НЕ админ
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	public function getUserProductsNeedModerationBySchemeAndNotAdmin($oScheme) {
		if (!$this->isAdministrator() and $this->getUserProductsNeedModerationByScheme($oScheme)) {
			return true;
		}
		return false;
	}


	/*
	 * --- Может модерировать ---
	 */

	/**
	 * Может ли пользователь модерировать продукты схемы
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	protected function getUserCanModerateProductsByScheme($oScheme) {
		/*
		 * есть ли назначенная группа прав, которая разрешает модерацию продуктов этим пользователем
		 */
		if ($oUserGroup = $this->GetUsergroupForThisUserByScheme($oScheme) and $oUserGroup->getUserCanModerateProductsEnabled()) {
			return true;
		}
		return false;
	}


	/**
	 * Может ли пользователь модерировать продукты схемы или это админ
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	public function getUserCanModerateProductsBySchemeOrIsAdmin($oScheme) {
		if ($this->getUserCanModerateProductsByScheme($oScheme) or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Категории ---
	 */

	/**
	 * Может ли пользователь управлять категориями
	 *
	 * @return bool
	 */
	protected function getCanUserManageCategories() {
		return false;
	}


	/**
	 * Может ли пользователь управлять категориями или это админ
	 *
	 * @return bool
	 */
	public function getCanUserManageCategoriesOrIsAdmin() {
		/*
		 * на будущее, сейчас только для админов
		 */
		if ($this->getCanUserManageCategories() or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Заказы магазина ---
	 */

	/**
	 * Может ли пользователь управлять заказами магазина
	 *
	 * @return bool
	 */
	protected function getCanUserManageOrders() {
		return false;
	}


	/**
	 * Может ли пользователь управлять заказами магазина или это админ
	 *
	 * @return bool
	 */
	public function getCanUserManageOrdersOrIsAdmin() {
		/*
		 * на будущее, сейчас только для админов
		 */
		if ($this->getCanUserManageOrders() or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Связи ---
	 */

	/**
	 * Может ли пользователь управлять связями
	 *
	 * @return bool
	 */
	protected function getCanUserManageLinks() {
		return false;
	}


	/**
	 * Может ли пользователь управлять связями или это админ
	 *
	 * @return bool
	 */
	public function getCanUserManageLinksOrIsAdmin() {
		/*
		 * на будущее, сейчас только для админов
		 */
		if ($this->getCanUserManageLinks() or $this->isAdministrator()) {
			return true;
		}
		return false;
	}


	/*
	 * --- Отложенная публикация продуктов ---
	 */

	/**
	 * Может ли пользователь использовать отложенную публикацию продуктов
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	protected function getUserCanDeferProductsByScheme($oScheme) {
		/*
		 * есть ли назначенная группа прав, которая разрешает использовать отложенную публикацию продуктов этим пользователем
		 */
		if ($oUserGroup = $this->GetUsergroupForThisUserByScheme($oScheme) and $oUserGroup->getUserCanDeferProductsEnabled()) {
			return true;
		}
		return false;
	}


	/**
	 * Может ли пользователь использовать отложенную публикацию продуктов или это админ
	 *
	 * @param $oScheme					сущность схемы
	 * @return bool
	 */
	public function getUserCanDeferProductsBySchemeOrIsAdmin($oScheme) {
		/*
		 * разрешено ли в схеме использовать отложенную публикацию продуктов
		 */
		if (!$oScheme->getAllowDeferredProductsEnabled()) {
			return false;
		}
		/*
		 * если это админ или есть права на использование
		 */
		if ($this->getUserCanDeferProductsByScheme($oScheme) or $this->isAdministrator()) {
			return true;
		}
		return false;
	}

}

?>