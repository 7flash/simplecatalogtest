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

if (!class_exists('Plugin')) {
	die('Kokobubble!');
}

class PluginSimplecatalog extends Plugin {

	/*
	 * Тип цели продуктов для комментариев
	 */
	const COMMENT_TARGET_TYPE_PRODUCT = 'product';

	/*
	 * Наследуемые объекты
	 */
	protected $aInherits = array(
		'action' => array(
			/*
			 * отображение созданных продуктов в профиле пользователя
			 */
			'ActionProfile',
		),
		'entity' => array(
			/*
			 * расширение сущности пользователя правами
			 */
			'ModuleUser_EntityUser',
			/*
			 * расширение сущности комментариев для работы с продуктами
			 */
			'ModuleComment_EntityComment',
			/*
			 * добавление нового типа валидатора - sc_method
			 */
			'ModuleValidate_EntityValidatorScMethod',
			/*
			 * добавление нового типа валидатора - sc_enum
			 */
			'ModuleValidate_EntityValidatorScEnum',
			/*
			 * добавление нового типа валидатора - sc_array
			 */
			'ModuleValidate_EntityValidatorScArray',
			/*
			 * добавление нового типа валидатора - sc_default
			 */
			'ModuleValidate_EntityValidatorScDefault',
		),
		'module' => array(
			/*
			 * расширение методов работы с бд
			 */
			'ModuleDatabase',
			/*
			 * расширение методов работы с комментариями для продуктов
			 */
			'ModuleComment',
			/*
			 * расширение возможностей работы со Smarty
			 */
			'ModuleViewer',
			/*
			 * работа с плагином sitemap
			 */
			'PluginSitemap_ModuleSitemap' => '_ModuleSitemap',
			/*
			 * расширение модуля пользователя короткими удобными методами (совместимые с лс 2.0)
			 */
			'ModuleUser',
			/*
			 * добавление метода проверки наличия продукта (типа цели) для подписки на комментарии продукта
			 */
			'ModuleSubscribe',
		),
		'mapper' => array(
			/*
			 * расширение методов работы с комментариями для продуктов
			 */
			'ModuleComment_MapperComment',
		),
	);


	public function Init() {
		/**
		 * Добавление нового типа цели
		 */
		$this->Subscribe_AddTargetType(self::COMMENT_TARGET_TYPE_PRODUCT . '_new_comment');
	}


	public function Activate() {
		@set_time_limit(0);
		ignore_user_abort(true);
		/*
		 * выполнить импорт дампов в бд
		 */
		$this->ExportSqlDumps();
		/*
		 * изменить перечисления типов таблиц
		 */
		$this->ModifyEnumsInDb();
		/*
		 * апгрейд таблиц для новых версий плагина (добавление полей)
		 */
		$this->UpgradeTablesInDb();
		/*
		 * обновить индексы у таблиц (добавить новые)
		 */
		$this->UpgradeIndexesInDb();

		return true;
	}


	/**
	 * Импортировать sql дампы в БД
	 */
	protected function ExportSqlDumps() {
		/*
		 * схема
		 */
		if (!$this->isTableExists('prefix_simplecatalog_scheme')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/scheme.sql');
		}
		/*
		 * поля схемы
		 */
		if (!$this->isTableExists('prefix_simplecatalog_scheme_fields')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/scheme_fields.sql');
		}
		/*
		 * продукты
		 */
		if (!$this->isTableExists('prefix_simplecatalog_product')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/product.sql');
		}
		/*
		 * поля продуктов
		 */
		if (!$this->isTableExists('prefix_simplecatalog_product_fields')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/product_fields.sql');
		}
		/*
		 * группы прав (пользователей)
		 */
		if (!$this->isTableExists('prefix_simplecatalog_usergroups')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/usergroups.sql');
		}
		/*
		 * назначения прав (групп прав пользователям)
		 */
		if (!$this->isTableExists('prefix_simplecatalog_userassign')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/userassign.sql');
		}
		/*
		 * категории
		 */
		if (!$this->isTableExists('prefix_simplecatalog_categories')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/categories.sql');
		}
		/*
		 * категории у продуктов
		 */
		if (!$this->isTableExists('prefix_simplecatalog_product_categories')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/product_categories.sql');
		}
		/*
		 * изображения (продуктов)
		 */
		if (!$this->isTableExists('prefix_simplecatalog_images')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/images.sql');
		}
		/*
		 * оформленные заказы (из корзины)
		 */
		if (!$this->isTableExists('prefix_simplecatalog_shop_orders')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/shop_orders.sql');
		}
		/*
		 * настройки связей для продуктов схем
		 */
		if (!$this->isTableExists('prefix_simplecatalog_scheme_links')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/scheme_links.sql');
		}
		/*
		 * связи объектов
		 */
		if (!$this->isTableExists('prefix_simplecatalog_links')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/links.sql');
		}
		/*
		 * метки на карте
		 */
		if (!$this->isTableExists('prefix_simplecatalog_maps')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/maps.sql');
		}
		/*
		 * универсальный счетчик
		 */
		if (!$this->isTableExists('prefix_simplecatalog_counter')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/counter.sql');
		}
	}


	/**
	 * Изменить перечисления типов в таблицах
	 */
	protected function ModifyEnumsInDb() {
		/*
		 * добавление нового типа комментариев для продуктов
		 */
		$this->addEnumType('prefix_comment', 'target_type', self::COMMENT_TARGET_TYPE_PRODUCT);
		/*
		 * tip: для прямого эфира новый тип будет модифицироваться при добавлении/редактировании схемы для каждой схемы отдельно
		 */
	}


	/**
	 * Выполнить обновление таблиц до более новых версий плагина (добавление полей)
	 */
	protected function UpgradeTablesInDb() {
		/*
		 * добавить в схему поле разрешения использования черновиков
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'allow_drafts')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_allow_drafts.sql');
		}
		/*
		 * добавить в продукт сео поля: заголовок, описание и ключевые слова
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_product', 'seo_title')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product/add_seo_title_description_keywords.sql');
		}
		/*
		 * добавить в схему поля: разрешения использования карт, выбора типа метки на карте и количества меток
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'map_items')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_map.sql');
		}
		/*
		 * добавить в поле схемы опции: просмотр только зарегистрированными и минимальный рейтинг пользователя для просмотра содержимого поля
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme_fields', 'for_auth_users_only')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme_fields/add_for_auth_users_only.sql');
		}
		/*
		 * добавить в поле продукта поле исходных вводимых данных
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_product_fields', 'content_source')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product_fields/add_content_source.sql');
		}
		/*
		 * добавить в схему поле ключевых слов
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'keywords')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_keywords.sql');
		}
		/*
		 * добавить в схему поле количества дней в течении которых автор продукта может его редактировать
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'days_author_can_manage_products_after_last_editing')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_days_author_can_manage_products_after_last_editing.sql');
		}
		/*
		 * добавить в настройки связей схемы поле количества продуктов для выбора в селекте для установки связи
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme_links', 'products_count_to_select')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme_links/add_products_count_to_select.sql');
		}
		/*
		 * добавить в права пользователей опцию возможности использования пользователем отложенной публикации
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_usergroups', 'user_can_defer_products')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/usergroups/add_user_can_defer_products.sql');
		}
		/*
		 * добавить в схему опцию использования отложенной публикации
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'allow_deferred_products')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_allow_deferred_products.sql');
		}
		/*
		 * добавить в права пользователей опцию возможности создания пользователем новых продуктов
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_usergroups', 'user_can_create_new_products')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/usergroups/add_user_can_create_new_products.sql');
		}
		/*
		 * добавить поле "id" для таблицы полей продуктов и примари индекс для него с автоинкрементом
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_product_fields', 'id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product_fields/add_id_and_primary_index.sql');
		}
		/*
		 * добавить в схему опцию вырезания точных размеров изображения заданных в схеме
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'exact_image_proportions')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_exact_image_proportions.sql');
		}
		/*
		 * добавить в схему опцию включения счетчика просмотров продукта
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'allow_count_views')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_allow_count_views.sql');
		}
		/*
		 * добавить в заказы айпи покупателя
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_shop_orders', 'ip')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/shop_orders/add_ip.sql');
		}
		/*
		 * добавить в схему выбор двух шаблонов отображения схемы и главного шаблона
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'template_name_first')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_template_names.sql');
		}
		/*
		 * добавить в поле схемы опцию разрешения редактирования поля продукта пользователем
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme_fields', 'editable_by_user')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme_fields/add_editable_by_user.sql');
		}
		/*
		 * добавить в поле схемы для типа поля "селект" опцию использования типа логики "И" для фильтра или "ИЛИ"
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme_fields', 'select_filter_items_using_and_logic')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme_fields/add_select_filter_items_using_and_logic.sql');
		}
		/*
		 * добавить в схему опцию разрешения ввода СЕО данных на странице продукта
		 */
		if (!$this->isFieldExists('prefix_simplecatalog_scheme', 'allow_edit_additional_seo_meta')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/add_allow_edit_additional_seo_meta.sql');
		}
	}


	/**
	 * Выполнить обновление индексов у таблиц (добавление)
	 */
	protected function UpgradeIndexesInDb() {
		/*
		 * есть ли индексы у таблицы связи категорий с продуктами
		 */
		if (!self::GetIndexExists('prefix_simplecatalog_product_categories', 'product_id_category_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product_categories/add_index_product_id_category_id.sql');
		}
		/*
		 * есть ли индексы у таблицы полей продукта
		 */
		if (!self::GetIndexExists('prefix_simplecatalog_product_fields', 'field_id_content_int')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product_fields/add_index_field_id_content_int_field_id_content_float_field_id_content_varchar.sql');
		}
		/*
		 * есть ли индекс у таблицы продуктов для цены
		 */
		if (!self::GetIndexExists('prefix_simplecatalog_product', 'price')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product/add_index_price.sql');
		}
		/*
		 * есть ли индексы у таблицы продуктов для сортировки
		 */
		if (!self::GetIndexExists('prefix_simplecatalog_product', 'add_date')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product/add_index_add_date_comment_count_fields_filled_count.sql');
		}

		/*
		 * удаление лишних индексов в таблице категорий
		 */
		if (self::GetIndexExists('prefix_simplecatalog_categories', 'url')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/categories/remove_index_url_and_other.sql');
		}
		/*
		 * удаление лишних индексов в таблице изображений
		 */
		if (self::GetIndexExists('prefix_simplecatalog_images', 'target_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/images/remove_index_target_id_and_other.sql');
		}
		/*
		 * удаление лишних индексов в таблице связей и добавление новых
		 */
		if (self::GetIndexExists('prefix_simplecatalog_links', 'parent_type_parent_id_from_target_type_from_target_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/links/remove_index_all.sql');
		}
		/*
		 * удаление лишних индексов в таблице меток карт и добавление новых
		 */
		if (self::GetIndexExists('prefix_simplecatalog_maps', 'target_type_target_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/maps/remove_index_all.sql');
		}
		/*
		 * удаление лишних индексов в таблице продуктов и добавление новых
		 */
		if (self::GetIndexExists('prefix_simplecatalog_product', 'scheme_id_moderation')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product/remove_index_scheme_id_moderation.sql');
		}
		/*
		 * удаление лишних индексов в таблице связей категорий и продуктов
		 */
		if (self::GetIndexExists('prefix_simplecatalog_product_categories', 'product_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product_categories/remove_index_product_id.sql');
		}
		/*
		 * удаление лишних индексов в таблице полей продукта
		 */
		if (self::GetIndexExists('prefix_simplecatalog_product_fields', 'product_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/product_fields/remove_index_product_id_and_other.sql');
		}
		/*
		 * удаление лишних индексов в таблице схем
		 */
		if (self::GetIndexExists('prefix_simplecatalog_scheme', 'scheme_url_active')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme/remove_index_scheme_url_and_other.sql');
		}
		/*
		 * удаление лишних индексов в таблице полей схем и добавление новых
		 */
		if (self::GetIndexExists('prefix_simplecatalog_scheme_fields', 'mandatory')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme_fields/remove_index_mandatory.sql');
		}
		/*
		 * удаление лишних индексов в таблице настроек связей схемы и добавление нового
		 */
		if (self::GetIndexExists('prefix_simplecatalog_scheme_links', 'active_scheme_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/scheme_links/remove_index_active_scheme_id.sql');
		}
		/*
		 * удаление лишних индексов в таблице заказов
		 */
		if (self::GetIndexExists('prefix_simplecatalog_shop_orders', 'delivery_type')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/shop_orders/remove_index_all.sql');
		}
		/*
		 * удаление лишних индексов в таблице группы прав
		 */
		if (self::GetIndexExists('prefix_simplecatalog_usergroups', 'scheme_id')) {
			$this->ExportSQL(dirname(__FILE__) . '/sql_dumps/upgrade/usergroups/remove_scheme_id.sql');
		}
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Существует ли индекс у таблицы
	 * При активации плагина его наследования ещё не работают и поэтому нельзя использовать добавленные методы в модуль Database, поэтому они размещены в этом файле
	 * Аналог этого метода в модуле БД будет добавлен в лс 2.0 (https://github.com/livestreet/livestreet-framework/issues/112)
	 *
	 * @param string $sTableName имя таблицы
	 * @param string $sIndexName имя индекса
	 * @param array  $aConfig
	 * @return bool
	 */
	static protected function GetIndexExists($sTableName, $sIndexName, $aConfig = null) {
		$sTableName = str_replace('prefix_', Config::Get('db.table.prefix'), $sTableName);
		$sQuery = "SHOW INDEX FROM `{$sTableName}`";
		if ($aRows = self::GetConnect($aConfig)->select($sQuery)) {
			foreach ($aRows as $aRow) {
				if ($aRow['Key_name'] == $sIndexName) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Выполнить подключение к БД
	 *
	 * @param $aConfig
	 * @return mixed
	 */
	static private function GetConnect($aConfig) {
		return LS::E()->Database_GetConnect($aConfig);
	}

}

?>