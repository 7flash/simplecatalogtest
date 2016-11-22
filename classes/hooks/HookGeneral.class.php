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

class PluginSimplecatalog_HookGeneral extends Hook {


	public function RegisterHook() {
		$this->AddHook('engine_init_complete', 'EngineInitComplete');
		$this->AddHook('template_body_begin', 'BodyBegin');
	}


	/**
	 * Добавление CSS и JS файлов и констант в шаблоны
	 */
	public function EngineInitComplete() {
		$sTemplateWebPath = Plugin::GetTemplateWebPath(__CLASS__);

		/*
		 * Подключение CSS файлов
		 */
		$this->LoadCssFiles($sTemplateWebPath);
		/*
		 * Подключение JS файлов
		 */
		$this->LoadJsFiles($sTemplateWebPath);
		/*
		 * Подключение кастомных CSS и JS файлов
		 */
		$this->LoadCustomAssets();
		/*
		 * Выполнить загрузку адаптации для шаблона (если он указан в конфиге)
		 */
		$this->LoadSpecialAssetsForNonStandartSkins();
		/*
		 * Назначение констант
		 */
		$this->AssignConstants();
	}


	/**
	 * Добавление кнопок в тулбар и модального окна корзины
	 *
	 * @return mixed
	 */
	public function BodyBegin() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'body_begin.tpl');
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Подключение CSS файлов
	 *
	 * @param $sTemplateWebPath				веб-путь к шаблону
	 */
	protected function LoadCssFiles($sTemplateWebPath) {
		/*
		 * добавление CSS файлов плагина
		 */
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/compatibility.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/misc.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/products.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/toolbars.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/blocks.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/profile.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/images-upload.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/shop.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/links.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/switch-group.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/categories.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/maps.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/typography.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/social-buttons.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'css/icons.css');

		/*
		 * добавление CSS файлов поставщиков
		 */
		$this->Viewer_AppendStyle($sTemplateWebPath . 'js/vendor/nouislider/jquery.nouislider.css');
		$this->Viewer_AppendStyle($sTemplateWebPath . 'js/vendor/icheck/skins/square/green.css');
	}


	/**
	 * Подключение JS файлов
	 *
	 * @param $sTemplateWebPath				веб-путь к шаблону
	 */
	protected function LoadJsFiles($sTemplateWebPath) {
		/*
		 * добавление JS файлов плагина
		 */
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/init.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/scheme.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/scheme_fields.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/scheme_links.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/product.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/product_compare.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/product_filter.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/product_edit.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/product_shop.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/product_shop_order.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/product_embed_code.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/userassign.js');
		// $this->Viewer_AppendScript($sTemplateWebPath . 'js/images.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/autocompleter.js');
		/*
		 * карты
		 */
		$sMapType = Config::Get('plugin.simplecatalog.maps.type');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/maps/' . $sMapType . '/map_editor.preset_storage.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/maps/' . $sMapType . '/map_editor.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/maps/' . $sMapType . '/map_helpers.js');

		/*
		 * добавление JS файлов поставщиков
		 */
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/vendor/jquery/jquery.ui.sortable.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/vendor/nouislider/jquery.nouislider.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/vendor/icheck/jquery.icheck.js');
		$this->Viewer_AppendScript($sTemplateWebPath . 'js/vendor/jquery.mousewheel/jquery.mousewheel.js');
	}


	/**
	 * Подключение кастомных CSS и JS файлов
	 */
	protected function LoadCustomAssets() {
		if (Config::Get('plugin.simplecatalog.general.assets.enable_custom_css_and_js_files')) {
			/*
			 * стили
			 */
			$sCustomCss = Plugin::GetPath(__CLASS__) . 'templates/skin/_custom/style.css';
			if (file_exists($sCustomCss)) {
				$this->Viewer_AppendStyle($sCustomCss);
			}
			/*
			 * скрипты
			 */
			$sCustomJs = Plugin::GetPath(__CLASS__) . 'templates/skin/_custom/script.js';
			if (file_exists($sCustomJs)) {
				$this->Viewer_AppendScript($sCustomJs);
			}
		}
	}


	/**
	 * Назначение констант в шаблонах
	 */
	protected function AssignConstants() {
		// general flags for components
		$this->Viewer_Assign('SC_COMPONENT_ENABLED', PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED);
		$this->Viewer_Assign('SC_COMPONENT_DISABLED', PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED);

		// places to show product fields
		$this->Viewer_Assign('SC_FIELD_SHOW_ANYWHERE', PluginSimplecatalog_ModuleScheme::FIELD_SHOW_ANYWHERE);
		$this->Viewer_Assign('SC_FIELD_SHOW_IN_PRODUCT_LIST', PluginSimplecatalog_ModuleScheme::FIELD_SHOW_IN_PRODUCT_LIST);
		$this->Viewer_Assign('SC_FIELD_SHOW_ON_PRODUCT_PAGE', PluginSimplecatalog_ModuleScheme::FIELD_SHOW_ON_PRODUCT_PAGE);
		$this->Viewer_Assign('SC_FIELD_SHOW_NOWHERE', PluginSimplecatalog_ModuleScheme::FIELD_SHOW_NOWHERE);

		// comments for products
		$this->Viewer_Assign('SC_ALLOW_COMMENTS_FORCED_TO_ALLOW', PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_FORCED_TO_ALLOW);
		$this->Viewer_Assign('SC_ALLOW_COMMENTS_DENY', PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_DENY);
		$this->Viewer_Assign('SC_ALLOW_COMMENTS_USER_DEFINED', PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_USER_DEFINED);

		// who can add/edit products (base settings)
		$this->Viewer_Assign('SC_CAN_ADD_PRODUCTS_ADMINS', PluginSimplecatalog_ModuleScheme::CAN_ADD_PRODUCTS_ADMINS);
		$this->Viewer_Assign('SC_CAN_ADD_PRODUCTS_ANY_USER', PluginSimplecatalog_ModuleScheme::CAN_ADD_PRODUCTS_ANY_USER);

		// product moderation status
		$this->Viewer_Assign('SC_MODERATION_DONE', PluginSimplecatalog_ModuleProduct::MODERATION_DONE);
		$this->Viewer_Assign('SC_MODERATION_NEEDED', PluginSimplecatalog_ModuleProduct::MODERATION_NEEDED);

		// what need to show on items product page (products or categories)
		$this->Viewer_Assign('SC_SHOW_ON_ITEMS_PAGE_LAST_PRODUCTS', PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_LAST_PRODUCTS);
		$this->Viewer_Assign('SC_SHOW_ON_ITEMS_PAGE_CATEGORIES', PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_CATEGORIES);
		$this->Viewer_Assign('SC_SHOW_ON_ITEMS_PAGE_MAP', PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_MAP);
	}


	/*
	 *
	 * --- Загрузка адаптаций для шаблонов ---
	 *
	 */

	/**
	 * Выполнить загрузку адаптации для шаблона (если он указан в конфиге)
	 */
	protected function LoadSpecialAssetsForNonStandartSkins() {
		/*
		 * если шаблон требует загрузки специальных стилей
		 */
		if (in_array(Config::Get('view.skin'), Config::Get('plugin.simplecatalog.general.assets.load_special_assets_for_skins'))) {
			/*
			 * попробовать загрузить его адаптацию (если есть)
			 */
			if (!$this->LoadSpecialAssetsForSkin(Config::Get('view.skin'))) {
				/*
				 * если адаптации нет - загрузить адаптацию по-умолчанию
				 */
				$this->LoadSpecialAssetsForSkin('default');
			}
		}
	}


	/**
	 * Загрузить адаптацию для указанного шаблона
	 *
	 * @param $sSkin			имя шаблона
	 * @return bool
	 */
	private function LoadSpecialAssetsForSkin($sSkin) {
		$sPath = Plugin::GetPath(__CLASS__) . 'templates/skin/_adaptations/' . $sSkin;
		/*
		 * если для данного шаблона есть адаптация
		 */
		if (is_dir($sPath)) {
			/*
			 * подключить все ксс файлы
			 */
			foreach ((array) glob($sPath . '/*.css') as $sCssFile) {
				$this->Viewer_AppendStyle($sCssFile);
			}
			return true;
		}
		return false;
	}

}

?>