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

class PluginSimplecatalog_ActionSchemePublic extends ActionPlugin {


	public function Init() {
		if (!SCRootStorage::IsInit()) {
			return Router::Action('error');
		}
	}


	protected function RegisterEvent() {
		/*
		 * переключение между шаблонами схемы
		 */
		$this->AddEventPreg('#^change-template$#', 'EventChangeTemplate');
		/*
		 * изменение количества элементов на страницу
		 */
		$this->AddEventPreg('#^change-items-per-page$#', 'EventChangeItemsPerPage');
	}


	/**
	 * Изменить шаблон схемы
	 *
	 * @return bool
	 */
	public function EventChangeTemplate() {
		$this->Security_ValidateSendForm();
		/*
		 * получить активную схему
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl($this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * валидация имени шаблона
		 */
		if (!in_array($sTemplate = $this->GetParam(1), array_keys($this->PluginSimplecatalog_Scheme_GetTemplatesAll()))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme.wrong_template_name'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * сохранить шаблон для схемы
		 */
		$_SESSION[PluginSimplecatalog_ModuleScheme::TEMPLATE_NAME_SESSION_KEY][$oScheme->getId()] = $sTemplate;
		$this->RedirectToReferer();
	}


	/**
	 * Установить количество элементов на страницу
	 *
	 * @return bool
	 */
	public function EventChangeItemsPerPage() {
		$this->Security_ValidateSendForm();
		/*
		 * получить активную схему
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * разрешено ли значение
		 */
		if (!in_array($iPerPage = $this->GetParam(1), $this->PluginSimplecatalog_Itemsperpage_GetValuesWithDefault($oScheme->getItemsPerPage()))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme.wrong_items_per_page'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * установить для текущего пользователя (в сессию) количество продуктов на страницу для этой схемы
		 */
		$this->PluginSimplecatalog_Itemsperpage_SetValueForScheme($oScheme->getId(), $iPerPage);
		$this->RedirectToReferer();
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

}

?>