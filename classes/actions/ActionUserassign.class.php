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

class PluginSimplecatalog_ActionUserassign extends ActionPlugin {

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
		if (!$this->oUserCurrent = $this->User_GetUserCurrent() or !$this->oUserCurrent->getCanUserManageUsersAssignToGroupsOrIsAdmin()) {
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
	 * Список назначений прав
	 */
	public function EventIndex() {
		$this->sMenuItemSelect = 'userassign';
		$this->sMenuSubItemSelect = 'index';
		$this->Viewer_Assign('aUserassign', $this->PluginSimplecatalog_Userassign_MyGetUserassignItemsAll());
	}


	/**
	 * Добавление пользователей к группе прав
	 *
	 * @return bool
	 */
	public function EventAdd() {
		$this->sMenuItemSelect = 'userassign';
		$this->sMenuSubItemSelect = 'add';

		/*
		 * получить активные группы прав
		 */
		if (!$aUsergroups = $this->PluginSimplecatalog_Usergroup_MyGetActiveUsergroupItems()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.No_Usergroups'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->Viewer_Assign('aUsergroups', $aUsergroups);

		if (isPost('submit_add')) {
			$this->Security_ValidateSendForm();

			/*
			 * разрешить использование нескольких логинов
			 */
			if (!is_string(getRequest('userlogins')) or !$aLogins = array_filter(array_map('trim', preg_split('#,#u', getRequest('userlogins'), -1, PREG_SPLIT_NO_EMPTY)))) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Wrong_Logins_List'), $this->Lang_Get('error'));
				return false;
			}

			$aEntities = array();
			foreach ($aLogins as $sLogin) {
				if (!$oUserToAssign = $this->User_GetUserByLogin($sLogin)) {
					$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.User_Not_Found', array('user' => $sLogin)), $this->Lang_Get('error'));
					return false;
				}
				$oEnt = Engine::GetEntity('PluginSimplecatalog_Userassign');
				$oEnt->setId(getRequest('id'));
				$oEnt->setUserId($oUserToAssign->getId());
				$oEnt->setGroupId((int) getRequest('group_id'));

				// for update process
				if ($oEnt->getId()) {
					$oEnt->_SetIsNew(false);
				}

				if (!$oEnt->_Validate()) {
					$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'));
					return false;
				}
				$aEntities[] = $oEnt;
			}

			/*
			 * сохранить все назначения
			 */
			foreach ($aEntities as $oEnt) {
				$oEnt->Save();
			}

			$this->Message_AddNotice('Ok', '', true);
			Router::Location(Router::GetPath('userassign'));
		}
	}


	/**
	 * Редактирование назначений прав
	 *
	 * @return bool|string
	 */
	public function EventEdit() {
		if (!$oUserassign = $this->PluginSimplecatalog_Userassign_MyGetUserassignById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Userassign_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuItemSelect = 'userassign';
		$this->sMenuSubItemSelect = 'add';

		/*
		 * получить активные группы прав
		 */
		if (!$aUsergroups = $this->PluginSimplecatalog_Usergroup_MyGetActiveUsergroupItems()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.No_Usergroups'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->Viewer_Assign('aUsergroups', $aUsergroups);

		$_REQUEST = array_merge($_REQUEST, $oUserassign->_getDataArray());
		/*
		 * трюк для получения логина
		 */
		$_REQUEST['userlogins'] = $oUserassign->getUser()->getLogin();

		$this->SetTemplateAction('add');
	}


	/**
	 * Удаление назначений прав
	 *
	 * @return bool|string
	 */
	public function EventDelete() {
		$this->Security_ValidateSendForm();
		if (!$oUserassign = $this->PluginSimplecatalog_Userassign_MyGetUserassignById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Userassign_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$oUserassign->Delete();
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