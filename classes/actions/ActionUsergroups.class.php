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

class PluginSimplecatalog_ActionUsergroups extends ActionPlugin {

	/*
	 * Сущность текущего пользователя
	 */
	protected $oUserCurrent = null;
	/*
	 * Для меню
	 */
	public $sMenuItemSelect = null;
	public $sMenuSubItemSelect = null;


	public function Init() {
		if (!$this->oUserCurrent = $this->User_GetUserCurrent() or !$this->oUserCurrent->getCanUserManageUserGroupsOrIsAdmin() ) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		$this->SetDefaultEvent('index');
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.simplecatalog.Title'));
	}


	protected function RegisterEvent() {
		$this->AddEventPreg('#^index$#', 'EventIndex');
		$this->AddEventPreg('#^add$#', 'EventAdd');
		$this->AddEventPreg('#^edit$#', 'EventEdit');
		$this->AddEventPreg('#^delete$#', 'EventDelete');
	}


	/**
	 * Список групп прав
	 */
	public function EventIndex() {
		$this->sMenuItemSelect = 'usergroups';
		$this->sMenuSubItemSelect = 'index';
		$this->Viewer_Assign('aUsergroups', $this->PluginSimplecatalog_Usergroup_MyGetUsergroupItemsAll());
	}


	/**
	 * Добавление группы прав
	 *
	 * @return bool
	 */
	public function EventAdd() {
		$this->sMenuItemSelect = 'usergroups';
		$this->sMenuSubItemSelect = 'add';

		/*
		 * получить список активных схем
		 */
		if (!$aSchemes = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.No_Schemes'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->Viewer_Assign('aSchemes', $aSchemes);

		if (isPost('submit_add')) {
			$this->Security_ValidateSendForm();

			$oEnt = Engine::GetEntity('PluginSimplecatalog_Usergroup');
			$oEnt->setId(getRequest('id'));
			$oEnt->setGroupName(getRequest('group_name'));
			$oEnt->setActive(getRequest('active'));
			$oEnt->setSchemeId((int) getRequest('scheme_id'));
			$oEnt->setCanUserEditProducts(getRequest('can_user_edit_products'));
			$oEnt->setUserProductsNeedModeration(getRequest('user_products_need_moderation'));
			$oEnt->setUserCanModerateProducts(getRequest('user_can_moderate_products'));
			$oEnt->setUserCanDeferProducts(getRequest('user_can_defer_products'));
			$oEnt->setUserCanCreateNewProducts(getRequest('user_can_create_new_products'));

			// for update process
			if ($oEnt->getId()) {
				$oEnt->_SetIsNew(false);
			}

			if (!$oEnt->_Validate()) {
				$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'));
				return false;
			}
			$oEnt->Save();

			$this->Message_AddNotice('Ok', '', true);
			Router::Location(Router::GetPath('usergroups'));
		}
	}


	/**
	 * Редактирование группы прав
	 *
	 * @return bool|string
	 */
	public function EventEdit() {
		if (!$oUsergroup = $this->PluginSimplecatalog_Usergroup_MyGetUsergroupById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Usergroup_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuItemSelect = 'usergroups';
		$this->sMenuSubItemSelect = 'add';

		/*
		 * получить список активных схем
		 */
		if (!$aSchemes = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.No_Schemes'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->Viewer_Assign('aSchemes', $aSchemes);

		$_REQUEST = array_merge($_REQUEST, $oUsergroup->_getDataArray());
		$this->SetTemplateAction('add');
	}


	/**
	 * Удаление группы прав
	 *
	 * @return bool|string
	 */
	public function EventDelete() {
		$this->Security_ValidateSendForm();
		if (!$oUsergroup = $this->PluginSimplecatalog_Usergroup_MyGetUsergroupById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Usergroup_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$oUsergroup->Delete();
		$this->Message_AddNotice('Ok', '', true);
		$this->RedirectToReferer();
	}


	/*
	 *
	 * --- Общие ---
	 *
	 */

	private function RedirectToReferer() {
		$this->PluginSimplecatalog_Tools_RedirectToReferer();
	}


	public function EventShutdown() {
		/*
		 * для меню
		 */
		$this->Viewer_AddMenu('simplecatalog_menu', Plugin::GetTemplatePath(__CLASS__) . 'navs/menu.simplecatalog.tpl');
		$this->Viewer_Assign('sMenuItemSelect', $this->sMenuItemSelect);
		$this->Viewer_Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
	}
	
}

?>