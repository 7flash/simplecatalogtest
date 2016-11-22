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
 * Управление настройками связей схем
 *
 */

class PluginSimplecatalog_ActionSchemeLinks extends ActionPlugin {

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
		/*
		 * проверка прав
		 */
		if (!$this->oUserCurrent = $this->User_GetUserCurrent() or !$this->oUserCurrent->getCanUserManageLinksOrIsAdmin() or !SCRootStorage::IsInit()) {
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
		/*
		 * изменить сортировку настроек связей схем
		 */
		$this->AddEventPreg('#^ajax-change-sorting-order$#', 'EventAjaxChangeSortingOrder');
	}


	/**
	 * Список настроек связей
	 */
	public function EventIndex() {
		$this->sMenuItemSelect = 'sc_links';
		$this->sMenuSubItemSelect = 'index';
	}


	/**
	 * Добавление настройки связи
	 *
	 * @return bool
	 */
	public function EventAdd() {
		$this->sMenuItemSelect = 'sc_links';
		$this->sMenuSubItemSelect = 'add';

		/*
		 * если была отправка формы
		 */
		if (isPost('submit_add')) {
			$this->Security_ValidateSendForm();

			/*
			 * получить схему для которой создается связь
			 */
			if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl($this->GetParam(0))) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
				return false;
			}

			$oEnt = Engine::GetEntity('PluginSimplecatalog_Scheme_Link');
			$oEnt->setId(getRequest('id'));
			$oEnt->setActive(getRequest('active'));
			$oEnt->setName(getRequest('name'));
			$oEnt->setDescription(getRequest('description'));
			$oEnt->setSchemeId($oScheme->getId());
			$oEnt->setTargetSchemeId(getRequest('target_scheme_id'));
			$oEnt->setType(getRequest('type'));
			$oEnt->setShowType(getRequest('show_type'));
			$oEnt->setSelectType(getRequest('select_type'));
			$oEnt->setSorting(getRequest('sorting'));
			$oEnt->setProductsCountToSelect(getRequest('products_count_to_select'));
			// for update process
			if ($oEnt->getId()) {
				$oEnt->_SetIsNew(false);
			}

			if (!$oEnt->_Validate()) {
				$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'));
				return false;
			}
			/*
			 * если выполнены условия для сохранения
			 */
			if ($oEnt->Save()) {
				$this->Message_AddNotice('Ok', '', true);
				Router::Location(Router::GetPath('sc_links') . 'index/' . $oScheme->getSchemeUrl());
			}
		}
	}


	/**
	 * Редактирование настройки связи
	 *
	 * @return bool|string
	 */
	public function EventEdit() {
		/*
		 * проверить существование связи
		 */
		if (!$oSchemeLink = $this->PluginSimplecatalog_Scheme_MyGetSchemeLinkById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme_links.not_found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuItemSelect = 'sc_links';
		$this->sMenuSubItemSelect = 'add';

		$_REQUEST = array_merge($_REQUEST, $oSchemeLink->_getDataArray());
		$this->SetTemplateAction('add');
	}


	/**
	 * Удаление настройки связи
	 *
	 * @return bool
	 */
	public function EventDelete() {
		$this->Security_ValidateSendForm();
		/*
		 * проверить существование связи
		 */
		if (!$oSchemeLink = $this->PluginSimplecatalog_Scheme_MyGetSchemeLinkById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme_links.not_found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$oSchemeLink->Delete();

		$this->Message_AddNotice('Ok', '', true);
		$this->RedirectToReferer();
	}


	/**
	 * Изменить порядок настроек связей схем
	 *
	 * @return bool
	 */
	public function EventAjaxChangeSortingOrder() {
		$this->Viewer_SetResponseAjax('json');
		/*
		 * получить массив ид в порядке сортировки
		 */
		$aIdsRaw = (array) getRequest('ids');
		/*
		 * получить настройки связей схемы с указанием в качестве ключа ид настроек связей
		 */
		if (!$aItems = $this->PluginSimplecatalog_Scheme_MyGetLinkItemsByIdIn($aIdsRaw, array('#index-from' => 'id'))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme_links.not_found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * выполнить сортировку
		 */
		$this->PluginSimplecatalog_Sorting_SortItemsByRawIdsAndORMSortOrderArray($aItems, $aIdsRaw, $this->PluginSimplecatalog_Scheme_GetDefaultSortingOrderForSchemeLinkSettings());

		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
	}


	/*
	 *
	 * --- Хелперы ---
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

		/*
		 * выбранная схема
		 */
		$sSchemeUrl = $this->GetParam(0);
		$this->Viewer_Assign('sMenuSchemeSelect', $sSchemeUrl);

		/*
		 * получить схему
		 */
		if ($sSchemeUrl and $oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl($sSchemeUrl)) {
			$this->Viewer_Assign('oScheme', $oScheme);
			/*
			 * получить связи для схемы
			 * tip: нужно только при выводе списка (index)
			 */
			$this->Viewer_Assign('aSchemeLinks', $this->PluginSimplecatalog_Scheme_MyGetSortedSchemeLinksForScheme($oScheme));
		}

		/*
		 * получить список схем для субменю
		 */
		$this->Viewer_Assign('aSchemesMenuItems', $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems());
	}
	
}

?>