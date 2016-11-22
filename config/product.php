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

$config = array();

/*
 *
 * --- SEO данные ---
 *
 */

/*
 * Минимальная длина ЧПУ продукта, которая разрешена для продукта.
 * Если заданная длина будет меньше - к ней будет добавлена случайная строка
 */
$config['product']['min_product_url_length'] = 3;

/*
 * Количество слов для вывода в мета-тег "description"
 */
$config['product']['seo']['description_words_count'] = 50;


/*
 *
 * --- Фильтр продуктов ---
 *
 */

/*
 * Нужно ли в фильтре по продуктам добавлять также дочерние категории выбранных категорий (т.е. всю ветку каждой выбранной для поиска категории)
 */
$config['product']['add_all_category_tree_for_product_filter'] = true;

/*
 * Нужно ли в фильтре по продуктам выводить заголовок не редактируемых полей (разделов)
 */
$config['product']['show_not_editable_fields_in_product_filter'] = true;


/*
 *
 * --- Скачивание файлов ---
 *
 */

/*
 * Нужно ли шифровать ссылки для загрузки файлов и защищать их от прямого скачивания файлов без захода на страницу продукта
 */
$config['product']['build_safe_and_hashed_links_for_file_downloads'] = true;

/*
 * Время жизни ссылки для скачивания файлов продукта (тип поля "файл"), дней
 */
$config['product']['safe_and_hashed_links_lifetime_days'] = 3;

/*
 * Включить подсчет количества скачиваний файлов
 */
$config['product']['count_file_downloads'] = true;


/*
 *
 * --- Блоки продуктов ---
 *
 */

/*
 * Количество последних продуктов схемы для отображения в блоке перед контентом или в сайдбаре
 */
$config['product']['last_products_items_count'] = 3;

/*
 * Список экшенов, где нужно показывать схемы и их последние продукты перед топиками (действует только для типа вывода последних продуктов "перед топиками"),
 * для типа "в блоке сайдбара" список экшенов задается в параметрах блока в config/blocks.php
 */
$config['product']['actions_to_show_last_products_before_content'] = array('index', 'blogs');


/*
 *
 * --- Вставка кода продуктов ---
 *
 */

/*
 * Разрешить использование вставки кода продуктов
 */
$config['product']['allow_embed_code'] = true;


/*
 *
 * --- Вывод продуктов ---
 *
 */

/*
 * Разрешенные типы сортировок для вывода списка продуктов
 */
$config['product']['allowed_sort_order_types'] = array(
	/*
	 * визуальный ключ => имя поля как оно задано в таблице
	 */
	'date' => 'add_date',
	'comments' => 'comment_count',
	'fullness' => 'fields_filled_count',
	/*
	 * если магазин для схемы не включен - эта сортировка показываться не будет
	 */
	'price' => 'price',
);

/*
 * Сортировка для продуктов по-умолчанию
 * tip: первое значение - имя поля в таблице продуктов,
 * 		второе - 'desc' - спадающая сортировка, 'asc' - возрастающая
 */
$config['product']['default_sort_order'] = array('add_date' => 'desc');


/*
 *
 * --- Ввод данных продукта ---
 *
 */

/*
 * При добавлении продукта из текстов удалять все теги если в настройках поля схемы не включен стандартный парсер лс
 * Если отключить эту опцию и стандартный парсер для поля в настройках поля схемы, то в значения полей можно будет вставлять что угодно в т.ч. скрипты, это не безопасно
 */
$config['product']['add']['strip_tags_when_default_parser_disabled'] = true;

return $config;

?>