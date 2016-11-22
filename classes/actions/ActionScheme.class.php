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

class PluginSimplecatalog_ActionScheme extends ActionPlugin {

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

		$this->oUserCurrent = $this->User_GetUserCurrent();

		if (!isset($this->oUserCurrent) or !is_object($this->oUserCurrent)) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}

		/*
		 * проверка прав
		 */
		if (!$this->oUserCurrent = $this->User_GetUserCurrent()
			or !$this->oUserCurrent->getCanUserManageSchemesOrIsAdmin()
			or !SCRootStorage::IsInit()
		) {
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
		 * изменить сортировку схем
		 */
		$this->AddEventPreg('#^ajax-change-sorting-order$#', 'EventAjaxChangeSortingOrder');
	}


	/**
	 * Список схем
	 */
	public function EventIndex() {
		$this->sMenuItemSelect = 'scheme';
		$this->sMenuSubItemSelect = 'index';
		$this->Viewer_Assign('aSchemes', $this->PluginSimplecatalog_Scheme_MyGetSchemeItemsAll());
	}


	/**
	 * Добавление схемы
	 *
	 * @return bool
	 */
	public function EventAdd() {
		$this->sMenuItemSelect = 'scheme';
		$this->sMenuSubItemSelect = 'add';

		$_REQUEST['scheme_template_names'] = $this->PluginSimplecatalog_Scheme_GetTemplatesAll();

		if (isPost('submit_add')) {
			$this->Security_ValidateSendForm();
			
			$oEnt = Engine::GetEntity('PluginSimplecatalog_Scheme');
			$oEnt->setId(getRequest('id'));
			$oEnt->setSchemeUrl(getRequest('scheme_url'));
			$oEnt->setSchemeName(getRequest('scheme_name'));
			$oEnt->setDescription(getRequest('description'));
			$oEnt->setKeywords(getRequest('keywords'));
			$oEnt->setActive(getRequest('active'));
			$oEnt->setMenuAddTopicCreate(getRequest('menu_add_topic_create'));
			$oEnt->setMenuMainAddLink(getRequest('menu_main_add_link'));
			$oEnt->setShortViewFieldsCount(getRequest('short_view_fields_count') ? getRequest('short_view_fields_count') : 2);
			$oEnt->setAllowComments(getRequest('allow_comments'));
			$oEnt->setAllowUserFriendlyUrl(getRequest('allow_user_friendly_url'));
			$oEnt->setAllowEditAdditionalSeoMeta(getRequest('allow_edit_additional_seo_meta'));
			$oEnt->setCanAddProducts(getRequest('can_add_products'));
			$oEnt->setModerationNeeded(getRequest('moderation_needed'));
			$oEnt->setShowFirstLetterGroups(getRequest('show_first_letter_groups'));
			$oEnt->setProfileShowLastProducts(getRequest('profile_show_last_products'));
			$oEnt->setProfileShowCreatedProducts(getRequest('profile_show_created_products'));
			$oEnt->setShowOnlineComments(getRequest('show_online_comments'));
			$oEnt->setMinUserRatingToCreateProducts((float) getRequest('min_user_rating_to_create_products'));
			$oEnt->setDaysAuthorCanManageProductsAfterLastEditing((int) getRequest('days_author_can_manage_products_after_last_editing'));
			$oEnt->setSorting((int) getRequest('sorting'));
			$oEnt->setItemsPerPage(getRequest('items_per_page') ? getRequest('items_per_page') : 15);
			$oEnt->setWhatToShowOnItemsPage(getRequest('what_to_show_on_items_page'));
			/*
			 * изображения
			 */
			$oEnt->setMaxImagesCount((int) getRequest('max_images_count'));
			$oEnt->setImageWidth(getRequest('image_width') ? getRequest('image_width') : 600);
			$oEnt->setImageHeight(getRequest('image_height') ? getRequest('image_height') : 400);
			$oEnt->setExactImageProportions(getRequest('exact_image_proportions'));

			$oEnt->setShop(getRequest('shop'));
			$oEnt->setBlockShowLastProducts(getRequest('block_show_last_products'));
			$oEnt->setAllowDrafts(getRequest('allow_drafts'));
			/*
			 * карты
			 */
			$oEnt->setMapItems(getRequest('map_items'));
			$oEnt->setSelectPresetForMapItems(getRequest('select_preset_for_map_items'));
			$oEnt->setMapItemsMax(getRequest('map_items_max') ? getRequest('map_items_max') : 15);

			$oEnt->setAllowDeferredProducts(getRequest('allow_deferred_products'));
			$oEnt->setAllowCountViews(getRequest('allow_count_views'));
			/*
			 * шаблоны
			 */
			$oEnt->setTemplateNameFirst(getRequest('template_name_first'));
			$oEnt->setTemplateNameSecond(getRequest('template_name_second'));
			$oEnt->setUseFirstTemplateAsDefault(getRequest('use_first_template_as_default'));

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
			Router::Location(Router::GetPath('scheme'));
		}
	}


	/**
	 * Редактирование схемы
	 *
	 * @return bool|string
	 */
	public function EventEdit() {
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetSchemeById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuItemSelect = 'scheme';
		$this->sMenuSubItemSelect = 'add';

		$_REQUEST = array_merge($_REQUEST, $oScheme->_getDataArray());
		$_REQUEST['scheme_template_names'] = $this->PluginSimplecatalog_Scheme_GetTemplatesAll();
		$this->SetTemplateAction('add');
	}


	/**
	 * Удаление схемы
	 *
	 * @return bool|string
	 */
	public function EventDelete() {
		$this->Security_ValidateSendForm();
		/*
		 * проверить существование такой схемы
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetSchemeById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$oScheme->Delete();
		$this->Message_AddNotice('Ok', '', true);
		$this->RedirectToReferer();
	}


	/**
	 * Изменить порядок схем
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
		 * получить схемы с указанием в качестве ключа ид схемы
		 */
		if (!$aItems = $this->PluginSimplecatalog_Scheme_MyGetSchemeItemsByIdIn($aIdsRaw, array('#index-from' => 'id'))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * выполнить сортировку
		 */
		$this->PluginSimplecatalog_Sorting_SortItemsByRawIdsAndORMSortOrderArray($aItems, $aIdsRaw, $this->PluginSimplecatalog_Scheme_GetDefaultSortingOrder());

		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Редирект на реферер
	 */
	private function RedirectToReferer() {
		$this->PluginSimplecatalog_Tools_RedirectToReferer();
	}


	public function EventShutdown() {
		//p($this, '$this');
		/*
		 * для меню
		 */
		$this->Viewer_AddMenu('simplecatalog_menu', Plugin::GetTemplatePath(__CLASS__) . 'navs/menu.simplecatalog.tpl');
		$this->Viewer_Assign('sMenuItemSelect', $this->sMenuItemSelect);
		$this->Viewer_Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
	}
	
}

?>