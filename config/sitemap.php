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
 * --- Продукты ---
 *
 */

/*
 * Количество продуктов каждой схемы на одну страницу сайтмапа.
 * Согласно протоколу сайтмапа на одну страницу может быть максимум 50к записей и страница не должна "весить" более 10 Мб.
 * Поэтому здесь усредненное значение в 30к т.к. могут быть длинные урлы (ЧПУ) продуктов, которые увеличивают "вес" одной страницы сайтмапа
 */
$config['sitemap']['products']['items_per_page'] = 30000;

/*
 * Приоритет продуктов
 * Значение от 0.0 до 1.0 (от наименьшего к наибольшему)
 */
$config['sitemap']['products']['priority'] = 0.9;

/*
 * Возможная частота изменения продуктов (редактирования уже созданных)
 * Значение из (в порядке убывания частоты): always (всегда), hourly (каждый час), daily (ежедневно), weekly (еженедельно), monthly (раз в месяц), yearly (раз в год), never (никогда)
 */
$config['sitemap']['products']['changefreq'] = 'weekly';

/*
 * Сортировка (ORM) продуктов для вывода в сайтмапе.
 * Не заполнена, продукты будут получены в порядке добавления в БД т.е. сначала самые старые, для быстрого получения продуктов из БД.
 * Может быть заменена, например, на "array('add_date' => 'desc')" чтобы на индексирование подавались сначала самые новые продукты
 */
$config['sitemap']['products']['sord_order'] = array();


/*
 *
 * --- Схемы (каталоги продуктов) ---
 *
 */

/*
 * Количество: считается что схем не может быть больше 50к
 */

/*
 * Приоритет схем
 */
$config['sitemap']['schemes']['priority'] = 0.8;

/*
 * Возможная частота изменения схем (страниц с продуктами)
 */
$config['sitemap']['schemes']['changefreq'] = 'daily';

return $config;

?>