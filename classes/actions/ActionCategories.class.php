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

class PluginSimplecatalog_ActionCategories extends ActionPlugin {

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
		if (!$this->oUserCurrent = $this->User_GetUserCurrent() or !$this->oUserCurrent->getCanUserManageCategoriesOrIsAdmin()) {
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
	 * Список категорий схемы
	 */
	public function EventIndex() {
		$this->sMenuItemSelect = 'sccategories';
		$this->sMenuSubItemSelect = 'index';
	}


	/**
	 * Добавление категории
	 *
	 * @return bool
	 */
	public function EventAdd() {
		$this->sMenuItemSelect = 'sccategories';
		$this->sMenuSubItemSelect = 'add';

		/*
		 * если было редактирование и ошибка при сохранении, то нужно снова запретить некорректные родительские категории
		 */
		if ($iCategoryId = getRequest('id') and $oCategory = $this->PluginSimplecatalog_Category_MyGetCategoryById($iCategoryId)) {
			/*
			 * получить список ид категорий, которые выбирать нельзя (чтобы не вложить категорию саму в себя и дочерние)
			 */
			$_REQUEST['blocked_categories_ids'] = $oCategory->getSelfAndDescendingCategoriesIds();
		}

		/*
		 * если была отправка формы
		 */
		if (isPost('submit_add')) {
			$this->Security_ValidateSendForm();
			/*
			 * получить схему для которой создается категория
			 */
			if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl($this->GetParam(0))) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
				return false;
			}

			$oEnt = Engine::GetEntity('PluginSimplecatalog_Category');
			$oEnt->setId(getRequest('id'));
			$oEnt->setUrl(getRequest('url'));
			$oEnt->setName(getRequest('name'));
			$oEnt->setParentId(getRequest('parent_id'));
			$oEnt->setSorting(getRequest('sorting'));
			$oEnt->setTargetType(PluginSimplecatalog_ModuleCategory::TARGET_TYPE_SCHEME);
			$oEnt->setTargetId($oScheme->getId());
			$oEnt->setDescription(getRequest('description'));

			// for update process
			if ($oEnt->getId()) {
				$oEnt->_SetIsNew(false);
			}

			if (!$oEnt->_Validate()) {
				$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'));
				return false;
			}

			/*
			 * обработка изображения (после валидации)
			 */
			if (getRequest('delete_image')) {
				$oEnt->setImageUrl($this->PluginSimplecatalog_Category_DeleteCategoryImage(
					isset($oCategory) ? $oCategory->getImageUrl() : ''
				));
			} else {
				$oEnt->setImageUrl($this->PluginSimplecatalog_Category_UploadCategoryImage(
					@$_FILES['image_url'],
					isset($oCategory) ? $oCategory->getImageUrl() : '',
					$oScheme
				));
			}

			/*
			 * если выполнены условия для сохранения
			 */
			if ($oEnt->Save()) {
				$this->Message_AddNotice('Ok', '', true);
				Router::Location(Router::GetPath('sccategories') . 'index/' . $oScheme->getSchemeUrl());
			}
		}
	}


	/**
	 * Редактирование категории
	 *
	 * @return bool|string
	 */
	public function EventEdit() {
		if (!$oCategory = $this->PluginSimplecatalog_Category_MyGetCategoryById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuItemSelect = 'sccategories';
		$this->sMenuSubItemSelect = 'add';

		$_REQUEST = array_merge($_REQUEST, $oCategory->_getDataArray());
		/*
		 * чтобы при ошибке валидации показывалось старое изображение
		 */
		$_REQUEST['image_url_original'] = $_REQUEST['image_url'];
		/*
		 * получить список ид категорий, которые выбирать нельзя (чтобы не вложить категорию саму в себя и дочерние)
		 */
		$_REQUEST['blocked_categories_ids'] = $oCategory->getSelfAndDescendingCategoriesIds();
		$this->SetTemplateAction('add');
	}


	/**
	 * Удаление категории
	 *
	 * @return bool
	 */
	public function EventDelete() {
		$this->Security_ValidateSendForm();
		/*
		 * проверить существование категории
		 */
		if (!$oCategory = $this->PluginSimplecatalog_Category_MyGetCategoryById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		/*
		 * прикреплена ли эта категория к продуктам - нужно ли переносить связи на родительскую категорию
		 */
		if ($oCategory->getTargetType() == PluginSimplecatalog_ModuleCategory::TARGET_TYPE_SCHEME) {
			/*
			 * продуктам этой и дочерних категорий установить родительскую категорию вместо них и пересчитать счетчики
			 */
			$this->PluginSimplecatalog_Category_TransferCategoryAndItsDescendantsToParentForAssignedProducts($oCategory);
		}

		/*
		 * удаление категории (и всех дочерних категорий), связи с продуктами уже заменены выше
		 */
		$oCategory->Delete();

		$this->Message_AddNotice('Ok', '', true);
		$this->RedirectToReferer();
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
		 * получить схему категории
		 */
		if ($sSchemeUrl and $oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl($sSchemeUrl)) {
			/*
			 * получить дерево категорий
			 */
			$this->Viewer_Assign('aCategoryTree', $this->PluginSimplecatalog_Category_MyBuildCategoryTreeForScheme($oScheme));
		}

		/*
		 * получить список схем для субменю
		 */
		$this->Viewer_Assign('aSchemesMenuItems', $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems());
	}
	
}

?>