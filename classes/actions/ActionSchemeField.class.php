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

class PluginSimplecatalog_ActionSchemeField extends ActionPlugin {

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
		 * проверка прав доступа
		 */
		if (!$this->oUserCurrent = $this->User_GetUserCurrent() or !$this->oUserCurrent->getCanUserEditSchemeFieldsOrIsAdmin() or !SCRootStorage::IsInit()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		$this->SetDefaultEvent('index');
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.simplecatalog.Title'));
	}


	protected function RegisterEvent() {
		$this->AddEventPreg('#^add$#', 'EventAdd');
		$this->AddEventPreg('#^schemefields$#', 'EventSchemeFields');
		$this->AddEventPreg('#^edit$#', 'EventEdit');
		$this->AddEventPreg('#^delete$#', 'EventDelete');
		/*
		 * изменить сортировку полей схемы
		 */
		$this->AddEventPreg('#^ajax-change-sorting-order$#', 'EventAjaxChangeSortingOrder');
	}


	/**
	 * Добавление поля схемы
	 *
	 * @return bool
	 */
	public function EventAdd() {
		$this->sMenuItemSelect = 'scheme_fields';
		$this->sMenuSubItemSelect = 'add';

		/*
		 * если была нажата кнопка "отправить"
		 */
		if (isPost('submit_add')) {
			$this->Security_ValidateSendForm();

			if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetSchemeById((int) getRequest('scheme_id'))) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
				return Router::Action('error');
			}
			$this->Viewer_Assign('oScheme', $oScheme);

			$oEnt = Engine::GetEntity('PluginSimplecatalog_Scheme_Fields');
			$oEnt->setId(getRequest('id'));
			$oEnt->setSchemeId($oScheme->getId());

			$oEnt->setTitle(getRequest('title'));
			$oEnt->setDescription(getRequest('description'));
			$oEnt->setMandatory(getRequest('mandatory'));
			$oEnt->setCode(getRequest('code'));

			$oEnt->setFieldType(getRequest('field_type'));
			/*
			 * текст до и после значения (префикс и постфикс)
			 */
			$oEnt->setValuePrefix(getRequest('value_prefix'));
			$oEnt->setValuePostfix(getRequest('value_postfix'));
			/*
			 * для сортировки полей
			 */
			$oEnt->setSorting(getRequest('sorting'));
			/*
			 * для парсинга значений через Jevix
			 */
			$oEnt->setRunParser(getRequest('run_parser'));
			/*
			 * для валидации значения через указанные валидаторы
			 */
			$oEnt->setValidator(getRequest('validator'));
			/*
			 * значение по-умолчанию для этого поля в новых продуктах
			 */
			$oEnt->setDefaultValue(getRequest('default_value'));
			/*
			 * видимость поля
			 */
			$oEnt->setPlacesToShowField(getRequest('places_to_show_field'));
			/*
			 * показывать заголовок поля при отображении значения продукта
			 */
			$oEnt->setShowFieldNamesInList(getRequest('show_field_names_in_list'));
			/*
			 * искать по этому полю
			 */
			$oEnt->setAllowSearchInThisField(getRequest('allow_search_in_this_field'));
			/*
			 * только зарегистрированным пользователям и минимальный рейтинг
			 */
			$oEnt->setForAuthUsersOnly(getRequest('for_auth_users_only'));
			$oEnt->setMinUserRatingToView((float) getRequest('min_user_rating_to_view'));

			$oEnt->setEditableByUser(getRequest('editable_by_user'));

			$oEnt->setTextMinLength(getRequest('text_min_length'));
			$oEnt->setTextMaxLength(getRequest('text_max_length'));

			$oEnt->setTextareaMinLength(getRequest('textarea_min_length'));
			$oEnt->setTextareaMaxLength(getRequest('textarea_max_length'));

			$oEnt->setFileMaxSize(getRequest('file_max_size'));
			$oEnt->setFileTypesAllowed(getRequest('file_types_allowed'));

			$oEnt->setSelectItems(getRequest('select_items'));
			$oEnt->setSelectMultipleItems(getRequest('select_multiple_items'));
			$oEnt->setSelectFilterItemsUsingAndLogic(getRequest('select_filter_items_using_and_logic'));
			
			/*
			 * for update process
			 */
			if ($oEnt->getId()) {
				$oEnt->_SetIsNew(false);
			}
			
			/*
			 * установить валидацию на основе типа поля
			 */
			$oEnt->_setValidateScenario($oEnt->getFieldType());

			if (!$oEnt->_Validate()) {
				$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'));
				return false;
			}
			$oEnt->Save();

			$this->Message_AddNotice('Ok', '', true);
			Router::Location($oScheme->getFieldsListWebPath());
		} else {
			/*
			 * загрузить схему
			 */
			if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetSchemeById((int) $this->GetParam(0))) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
				$this->RedirectToReferer();
				return false;
			}
			$_REQUEST['scheme_id'] = $oScheme->getId();
			$this->Viewer_Assign('oScheme', $oScheme);
			/*
			 * если в схеме ещё нет полей, то вывести сообщение, что это будет заголовок
			 */
			if (count($oScheme->getFields()) == 0) {
				$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.Fields.Add.first_field_is_title'));
			}
		}
	}


	/**
	 * Список полей схемы
	 *
	 * @return bool
	 */
	public function EventSchemeFields() {
		$this->sMenuItemSelect = 'scheme_fields';
		$this->sMenuSubItemSelect = 'schemefields';

		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetSchemeById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aSchemeFields', $oScheme->getFields());
	}


	/**
	 * Редактирование поля схемы
	 *
	 * @return bool
	 */
	public function EventEdit() {
		if (!$oField = $this->PluginSimplecatalog_Scheme_MyGetFieldsById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Field_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuItemSelect = 'scheme_fields';
		$this->sMenuSubItemSelect = 'add';

		$_REQUEST = array_merge($_REQUEST, $oField->_getDataArray());
		$this->Viewer_Assign('oScheme', $oField->getScheme());

		$this->SetTemplateAction('add');
	}


	/**
	 * Удаление поля схемы
	 *
	 * @return bool
	 */
	public function EventDelete() {
		$this->Security_ValidateSendForm();
		if (!$oField = $this->PluginSimplecatalog_Scheme_MyGetFieldsById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Field_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$oField->Delete();
		$this->Message_AddNotice('Ok', '', true);
		$this->RedirectToReferer();
	}


	/**
	 * Изменить порядок полей схемы
	 *
	 * @return bool
	 */
	public function EventAjaxChangeSortingOrder() {
		$this->Viewer_SetResponseAjax('json');
		/*
		 * получить ид схемы
		 */
		$iSchemeId = (int) getRequest('scheme_id');
		/*
		 * получить массив ид полей в порядке сортировки
		 */
		$aIdsRaw = (array) getRequest('ids');
		/*
		 * получить поля схемы с указанием в качестве ключей ид полей
		 */
		if (!$aItems = $this->PluginSimplecatalog_Scheme_MyGetFieldsItemsBySchemeIdAndIdIn($iSchemeId, $aIdsRaw, array('#index-from' => 'id'))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Field_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * выполнить сортировку
		 */
		$this->PluginSimplecatalog_Sorting_SortItemsByRawIdsAndORMSortOrderArray($aItems, $aIdsRaw, $this->PluginSimplecatalog_Scheme_GetDefaultSortingOrderForSchemeFields());

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
	}
	
}

?>