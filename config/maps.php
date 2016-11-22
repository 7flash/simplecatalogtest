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
 * Тип карт (поддерживается только 'yandex')
 */
$config['maps']['type'] = 'yandex';

/*
 *
 * --- Общие настройки карт ---
 *
 */

/*
 * Центр карты (при создании нового продукта карта будет отцентрована по этому месту)
 *
 * Получить координаты нужного места можно через сервис Яндекса
 * http://dimik.github.io/ymaps/examples/location-tool/
 *
 * Координаты записываются в порядке "широта, долгота"
 */
$config['maps']['settings']['base']['center'] = '50.450323, 30.525918';

/*
 * Масштаб по-умолчанию (зум) (0..18)
 *
 * Получить нужное число зума можно через сервис, который описан в предыдущем параметре
 */
$config['maps']['settings']['base']['zoom'] = 16;

return $config;

?>