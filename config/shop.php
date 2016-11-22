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
 * Настройки магазина
 *
 */

/*
 * Текущая валюта цен для всех схем (USD, RUB, UAH)
 */
$config['product']['shop']['currency_default'] = 'RUB';

/*
 * Отправлять письмо при получении нового заказа в магазине.
 * Указать адрес почты (test@gmail.com) или ид пользователя (1), на почту которого (указанную в профиле) должно прийти уведомление
 * Если установить в null, то письма отправляться не будут
 */
$config['product']['shop']['new_order_receiver'] = 1;

/*
 * Показывать поле количества заказываемых продуктов возле кнопки "купить" и кнопки "+" и "-"
 * (поле для смены количества в корзине будет в любом случае)
 */
$config['product']['shop']['item_count_field_near_buy_button'] = true;

/*
 * Максимальное количество новых заказов (не обработанных) с одного айпи
 */
$config['product']['shop']['max_orders_count_per_ip'] = 5;

return $config;

?>