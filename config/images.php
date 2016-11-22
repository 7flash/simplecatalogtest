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
 * Параметры загрузки изображений к объектам
 *
 */

/*
 * разрешенные к выбору расширения файлов изображений
 */
$config['images']['allowed_extensions'] = 'png, jpeg, jpg, gif, jpe';

/*
 * максимальный размер изображения для загрузки в байтах
 */
$config['images']['upload_max_file_size'] = 800 * 1000;

/*
 * путь к изображению продукта по-умолчанию (относительно шаблона)
 */
$config['images']['default_product_image'] = 'images/sc_avatar_alternative.png';

return $config;

?>