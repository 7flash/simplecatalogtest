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

class PluginSimplecatalog_ModuleShop_EntityOrder extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('name', 'string', 'min' => 2, 'max' => 40, 'allowEmpty' => false),
		
		array('phone', 'regexp', 'pattern' => '#^[+\d() -]{5,20}$#', 'allowEmpty' => false),
		array('comment', 'string', 'max' => 500, 'allowEmpty' => true),

		array('delivery_type', 'delivery_type'),

		array('geo_name', 'string', 'max' => 100, 'allowEmpty' => true),
		array('exact_adress', 'string', 'max' => 100, 'allowEmpty' => true),
		array('receiver_name', 'string', 'max' => 40, 'allowEmpty' => true),

		array('payment_type', 'payment_type'),

		array('cart_data', 'string', 'max' => 1000, 'allowEmpty' => false),
		array('total_price', 'number', 'allowEmpty' => false),

		array('new', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),

		array('ip', 'regexp', 'pattern' => '#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', 'allowEmpty' => false),
		array('ip', 'ip'),

		/*
		 * tip: часть полей заполняется автоматически
		 */
	);


	/**
	 * Вызывается перед сохранением сущности
	 *
	 * @return bool|void
	 */
	protected function beforeSave() {
		/*
		 * если сущность новая - поставить дату и автора
		 */
		if ($this->_isNew()) {
			$this->setDateAdd(date('Y-m-d H:i:s'));
			/*
			 * авторизирован ли пользователь
			 */
			if ($this->User_GetUserCurrent()) {
				$this->setUserId($this->User_GetUserCurrent()->getId());
			}
		}
		return parent::beforeSave();
	}


	/**
	 * Вызывается перед удалением сущности
	 *
	 * @return bool
	 */
	protected function beforeDelete() {
		return parent::beforeDelete();
	}


	/*
	 *
	 * --- Валидация ---
	 *
	 */

	/**
	 * Валидация типа доставки
	 *
	 * @param $mValue			значение
	 * @param $aParams			параметры
	 * @return bool
	 */
	public function validateDeliveryType($mValue, $aParams) {
		if (!in_array($mValue, array(PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_SELF, PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_COURIER))) {
			return $this->Lang_Get('plugin.simplecatalog.Errors.orders.validate.unknown_delivery_type');
		}
		return true;
	}


	/**
	 * Валидация типа оплаты
	 *
	 * @param $mValue			значение
	 * @param $aParams			параметры
	 * @return bool
	 */
	public function validatePaymentType($mValue, $aParams) {
		if (!in_array($mValue, array(PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_CASH, PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_NONCASH))) {
			return $this->Lang_Get('plugin.simplecatalog.Errors.orders.validate.unknown_payment_type');
		}
		return true;
	}


	/**
	 * Валидация айпи
	 *
	 * @param $mValue			значение
	 * @param $aParams			параметры
	 * @return bool
	 */
	public function validateIp($mValue, $aParams) {
		/*
		 * достигнут ли лимит количества новых заказов для этого айпи
		 */
		if ($this->PluginSimplecatalog_Shop_GetNewOrdersLimitExceedByIp($mValue)) {
			return $this->Lang_Get('plugin.simplecatalog.Errors.orders.validate.orders_count_limit_exceed_per_ip');
		}
		return true;
	}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Новый ли это заказ
	 *
	 * @return bool
	 */
	public function getNewEnabled() {
		return $this->getNew() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/*
	 *
	 * --- Урлы ---
	 *
	 */

	/**
	 * Получить урл для просмотра заказа
	 *
	 * @return string
	 */
	public function getViewUrl() {
		return Router::GetPath('shop') . 'order/view/' . $this->getId();
	}


	/**
	 * Получить урл для удаления заказа
	 *
	 * @return string
	 */
	public function getDeleteUrl() {
		return Router::GetPath('shop') . 'order/delete/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/**
	 * Получить урл для установки статуса "выполнен" заказа
	 *
	 * @return string
	 */
	public function getChangeStatusDoneUrl() {
		return Router::GetPath('shop') . 'order/setdone/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}


}

?>