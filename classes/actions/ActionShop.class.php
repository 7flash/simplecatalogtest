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

/*
 *
 * Магазин
 *
 */

class PluginSimplecatalog_ActionShop extends ActionPlugin {

	/*
	 * Сущность текущего пользователя
	 */
	protected $oUserCurrent = null;
	/*
	 * Для меню
	 */
	public $sMenuItemSelect = null;


	public function Init() {
		/*
		 * для заказа не обязательно нужна авторизация, доступ для всех
		 */
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->SetDefaultEvent('index');
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.simplecatalog.shop.title'));
		if (!SCRootStorage::IsInit()) {
			return Router::Action('error');
		}
	}


	protected function RegisterEvent() {
		/*
		 * добавить/пересчитать продукт в корзину
		 */
		$this->AddEventPreg('#^cart$#', '#^ajax-add-to-cart$#', 'EventAjaxAddToCart');
		/*
		 * удалить продукт из корзины
		 */
		$this->AddEventPreg('#^cart$#', '#^ajax-remove-cart-item$#', 'EventAjaxRemoveCartItem');
		/*
		 * список продуктов корзины
		 */
		$this->AddEventPreg('#^cart$#', '#^ajax-get-cart-items$#', 'EventAjaxShowCartItems');

		/*
		 * оформление заказа
		 */
		$this->AddEventPreg('#^order$#', '#^$#', 'EventOrder');
		/*
		 * страница с сообщением что заказ оформлен
		 */
		$this->AddEventPreg('#^done$#', '#^$#', 'EventOrderDone');
		/*
		 * список заказов
		 */
		$this->AddEventPreg('#^index$#', '#^$#', 'EventIndex');
		/*
		 * просмотр заказа
		 */
		$this->AddEventPreg('#^order$#', '#^view$#', '#^\d+$#', 'EventOrderView');
		/*
		 * удаление заказа
		 */
		$this->AddEventPreg('#^order$#', '#^delete$#', '#^\d+$#', 'EventOrderDelete');
		/*
		 * установка статуса что заказ выполнен
		 */
		$this->AddEventPreg('#^order$#', '#^setdone$#', '#^\d+$#', 'EventOrderSetToDone');
	}


	/*
	 *
	 * --- Аякс обработчики ---
	 *
	 */

	/**
	 * Добавление продукта в корзину
	 *
	 * @return bool
	 */
	public function EventAjaxAddToCart() {
		$this->Viewer_SetResponseAjax('json');
		/*
		 * существует ли такой продукт
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) getRequest('product_id')) or !$oProduct->getModerationDone() or !$oProduct->getPrice()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Product_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * добавить продукт в корзину
		 */
		$this->PluginSimplecatalog_Shop_AddProductToCart($oProduct, (int) getRequest('count'));
		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
		/*
		 * отрендерить шаблон со списком продуктов
		 */
		$this->RenderCartItemsHtml();
	}


	/**
	 * Удаление продукта из корзины
	 *
	 * @return bool
	 */
	public function EventAjaxRemoveCartItem() {
		$this->Viewer_SetResponseAjax('json');
		/*
		 * существует ли такой продукт
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) getRequest('product_id')) or !$oProduct->getModerationDone()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Product_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * удалить продукт из корзины
		 */
		$this->PluginSimplecatalog_Shop_RemoveProductFromCart($oProduct);
		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
		/*
		 * отрендерить шаблон со списком продуктов
		 */
		$this->RenderCartItemsHtml();
	}


	/**
	 * Получить список продуктов корзины
	 */
	public function EventAjaxShowCartItems() {
		$this->Viewer_SetResponseAjax('json');
		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
		/*
		 * отрендерить шаблон со списком продуктов
		 */
		$this->RenderCartItemsHtml();
	}


	/*
	 *
	 * --- Страницы ---
	 *
	 */

	/**
	 * Оформление заказа
	 */
	public function EventOrder() {
		/*
		 * если была отправка данных
		 */
		if (isPost('submit_order')) {
			$this->SubmitOrder();
		}
		/*
		 * список продуктов корзины
		 */
		$aCartData = $this->PluginSimplecatalog_Shop_GetProductsFromCart();
		if (!$aCartData['count']) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.shop.cart.empty_cart'), $this->Lang_Get('error'));
			return false;
		}
		$this->Viewer_Assign('aCartData', $aCartData);
		/*
		 * если есть текущий пользователь - заполнить часть полей
		 */
		if ($this->oUserCurrent and !isPost('submit_order')) {
			$_REQUEST['name'] = $_REQUEST['receiver_name'] = $this->oUserCurrent->getProfileName();
		}
		/*
		 * загрузить список стран
		 */
		$aCountries = $this->Geo_GetCountries(array(), array('sort' => 'asc'), 1, 300);
		$this->Viewer_Assign('aGeoCountries', $aCountries['collection']);
		/*
		 * если была выбрана страна - загрузить регионы
		 */
		if (isPost('geo_country')) {
			$aRegions = $this->Geo_GetRegions(array('country_id' => (int) getRequest('geo_country')), array('sort' => 'asc'), 1, 500);
			$this->Viewer_Assign('aGeoRegions', $aRegions['collection']);
		}
		/*
		 * если был выбран регион - загрузить города
		 */
		if (isPost('geo_region')) {
			$aCities = $this->Geo_GetCities(array('region_id' => (int) getRequest('geo_region')), array('sort' => 'asc'), 1, 500);
			$this->Viewer_Assign('aGeoCities', $aCities['collection']);
		}
	}


	/**
	 * Добавление нового заказа
	 */
	protected function SubmitOrder() {
		$this->Security_ValidateSendForm();

		/*
		 * получить гео данные
		 */
		if (getRequest('geo_city')) {
			$oGeoObject = $this->Geo_GetGeoObject('city', getRequestStr('geo_city'));
		} elseif (getRequest('geo_region')) {
			$oGeoObject = $this->Geo_GetGeoObject('region', getRequestStr('geo_region'));
		} elseif (getRequest('geo_country')) {
			$oGeoObject = $this->Geo_GetGeoObject('country', getRequestStr('geo_country'));
		} else {
			$oGeoObject = null;
		}
		$sGeoName = null;
		if ($oGeoObject) {
			if ($oCountry = $oGeoObject->getCountry()) {
				$sGeoName = $oCountry->getName();
			}
			if ($oRegion = $oGeoObject->getRegion()) {
				$sGeoName = $oRegion->getName();
			}
			if ($oCity = $oGeoObject->getCity()) {
				$sGeoName = $oCity->getName();
			}
		}
		/*
		 * получить данные заказа
		 */
		$aCartData = $this->PluginSimplecatalog_Shop_GetProductsFromCart();
		/*
		 * создать заказ
		 */
		$oEnt = Engine::GetEntity('PluginSimplecatalog_Shop_Order');
		$oEnt->setName($this->Text_JevixParser(getRequest('name')));
		$oEnt->setPhone($this->Text_JevixParser(getRequest('phone')));
		$oEnt->setComment($this->Text_JevixParser(getRequest('comment')));
		$oEnt->setDeliveryType(getRequest('delivery_type'));
		$oEnt->setGeoName($this->Text_JevixParser($sGeoName));
		$oEnt->setExactAdress($this->Text_JevixParser(getRequest('exact_adress')));
		$oEnt->setReceiverName($this->Text_JevixParser(getRequest('receiver_name')));
		$oEnt->setPaymentType(getRequest('payment_type'));
		$oEnt->setCartData(serialize($this->PluginSimplecatalog_Shop_GetProductIdsFromCart()));
		$oEnt->setTotalPrice($aCartData['total_price']);
		$oEnt->setNew(PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED);
		$oEnt->setIp(func_getIp());

		if (!$oEnt->_Validate()) {
			$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'));
			return false;
		}
		$oEnt->Save();

		/*
		 * очистить корзину
		 */
		$this->PluginSimplecatalog_Shop_ResetCart();

		/*
		 * отправить уведомление о новом заказе
		 */
		$this->SendNotifyAboutNewOrder();

		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'), '', true);
		Router::Location(Router::GetPath('shop') . 'done');
		return true;
	}


	/**
	 * Отправить уведомление на почту о новом заказе указанному в конфиге получателю
	 *
	 * @return bool
	 */
	protected function SendNotifyAboutNewOrder() {
		/*
		 * получатель письма (ид пользователя или почта)
		 */
		$mUser = Config::Get('plugin.simplecatalog.product.shop.new_order_receiver');
		switch(gettype($mUser)) {
			/*
			 * указан ид пользователя
			 */
			case 'integer':
				if (!$mUser = $this->User_GetUserById($mUser)) {
					return false;
				}
				break;
			/*
			 * указана почта
			 */
			case 'string':
				if (!$this->Validate_Validate('email', $mUser, array('allowEmpty' => false))) {
					return false;
				}
				break;
			/*
			 * неизвестный тип параметра
			 */
			default:
				return false;
		}
		/*
		 * отправить письмо
		 */
		$this->Notify_Send(
			$mUser,
			'email.shop.new_order.tpl',
			$this->Lang_Get('plugin.simplecatalog.shop.mail.new_order.title'),
			array(),
			'simplecatalog'
		);
		return true;
	}


	/**
	 * Страница с сообщением что заказ оформлен
	 */
	public function EventOrderDone() {
		$this->SetTemplateAction('done');
	}


	/**
	 * Список заказов
	 */
	public function EventIndex() {
		$this->SetTemplateAction('index');
		$this->sMenuItemSelect = 'sc_shop';
		/*
		 * проверить права доступа
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanUserManageOrdersOrIsAdmin()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		$this->Viewer_Assign('aOrders', $this->PluginSimplecatalog_Shop_MyGetOrderItemsAll());
	}


	/**
	 * Просмотр заказа
	 */
	public function EventOrderView() {
		$this->SetTemplateAction('view');
		$this->sMenuItemSelect = 'sc_shop';
		/*
		 * проверить права доступа
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanUserManageOrdersOrIsAdmin()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		/*
		 * существует ли такой заказ
		 */
		if (!$oOrder = $this->PluginSimplecatalog_Shop_MyGetOrderById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.orders.order_not_found'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		$this->Viewer_Assign('oOrder', $oOrder);
		$this->Viewer_Assign('aCartData', $this->PluginSimplecatalog_Shop_CalcCartDataByProductsArrayIds(unserialize($oOrder->getCartData())));
	}


	/**
	 * Удаление заказа
	 */
	public function EventOrderDelete() {
		$this->Security_ValidateSendForm();
		/*
		 * проверить права доступа
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanUserManageOrdersOrIsAdmin()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		/*
		 * существует ли такой заказ
		 */
		if (!$oOrder = $this->PluginSimplecatalog_Shop_MyGetOrderById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.orders.order_not_found'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		$oOrder->Delete();
		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'), '', true);
		Router::Location(Router::GetPath('shop'));
	}


	/**
	 * Установка статуса "выполнен" для заказа
	 */
	public function EventOrderSetToDone() {
		$this->Security_ValidateSendForm();
		/*
		 * проверить права доступа
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanUserManageOrdersOrIsAdmin()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		/*
		 * существует ли такой заказ
		 */
		if (!$oOrder = $this->PluginSimplecatalog_Shop_MyGetOrderById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.orders.order_not_found'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		$oOrder->setNew(PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED);
		$oOrder->Save();
		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'), '', true);
		Router::Location(Router::GetPath('shop'));
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Получить отрендеренный шаблон со списком продуктов корзины и рассчитаной стоимостью
	 */
	protected function RenderCartItemsHtml() {
		$aCartData = $this->PluginSimplecatalog_Shop_GetProductsFromCart();

		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('aCartData', $aCartData);

		$this->Viewer_AssignAjax('sText', $oViewer->Fetch(Plugin::GetTemplatePath(__CLASS__) . 'helpers/cart/list.tpl'));
		$this->Viewer_AssignAjax('bEmptyCart', $aCartData['count'] == 0);
	}


	public function EventShutdown() {
		/*
		 * для меню
		 */
		$this->Viewer_AddMenu('simplecatalog_menu', Plugin::GetTemplatePath(__CLASS__) . 'navs/menu.simplecatalog.tpl');
		$this->Viewer_Assign('sMenuItemSelect', $this->sMenuItemSelect);
	}


}
