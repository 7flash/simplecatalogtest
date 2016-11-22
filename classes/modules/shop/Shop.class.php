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
 * --- Модуль работы с магазином ---
 *
 */

/*
 * tip: в корзине массив, в котором ключ = ид продукта, значение = количество
 */

class PluginSimplecatalog_ModuleShop extends ModuleORM {

	/*
	 * Сортировка для заказов по-умолчанию
	 */
	protected $aDefaultSortingOrder = array('date_add' => 'desc');

	/*
	 * Ключ сессии в котором хранятся ид продуктов из корзины пользователя
	 */
	const SESSION_CART_PRODUCTS_KEY = 'sc_shop_cart';

	/*
	 * Типы доставки
	 */
	const DELIVERY_TYPE_SELF = 1;
	const DELIVERY_TYPE_COURIER = 2;

	/*
	 * Типы оплаты
	 */
	const PAYMENT_TYPE_CASH = 1;
	const PAYMENT_TYPE_NONCASH = 2;


	public function Init() {
		/*
		 * для ORM
		 */
		parent::Init();
	}


	/**
	 * Получить отображаемое значение установленной валюты
	 *
	 * @return mixed
	 */
	public function GetCurrencyDisplayValue() {
		return $this->Lang_Get('plugin.simplecatalog.currencies.' . Config::Get('plugin.simplecatalog.product.shop.currency_default'));
	}


	/**
	 * Добавить продукт и его количество в корзину
	 *
	 * @param     $oProduct			сущность продукта
	 * @param int $iCount			количество
	 * @return bool
	 */
	public function AddProductToCart($oProduct, $iCount = 1) {
		if (!$iCount) {
			return false;
		}
		$aData = $this->GetProductIdsFromCart();
		$aData[$oProduct->getId()] = $iCount;
		$this->SetProductsToCart($aData);
		return true;
	}


	/**
	 * Удалить продукт из корзины
	 *
	 * @param $oProduct				сущность продукта
	 */
	public function RemoveProductFromCart($oProduct) {
		$aData = $this->GetProductIdsFromCart();
		unset($aData[$oProduct->getId()]);
		$this->SetProductsToCart($aData);
	}


	/**
	 * Получить массив продуктов и их количества в корзине
	 *
	 * @return array
	 */
	public function GetProductsFromCart() {
		return $this->CalcCartDataByProductsArrayIds($this->GetProductIdsFromCart());
	}


	/**
	 * Рассчитать данные для отображения корзины по массиву ид продуктов
	 *
	 * @param array $aProductsIds			массив ид продуктов
	 * @return array
	 */
	public function CalcCartDataByProductsArrayIds($aProductsIds = array()) {
		$aData = array();
		/*
		 * общая сумма всего заказа
		 */
		$fTotalPrice = 0;
		foreach ($aProductsIds as $iId => $iCount) {
			/*
			 * существует ли такой промодерированный продукт по ид у активной схемы
			 */
			if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById((int) $iId)) {
				continue;
			}
			/*
			 * сумма по продукту
			 */
			$fSumm = $oProduct->getActualPrice() * $iCount;
			$oProduct->setSummaryPrice($fSumm);
			/*
			 * добавить к общей сумме заказа
			 */
			$fTotalPrice += $fSumm;

			$aData[] = array('oProduct' => $oProduct, 'iCount' => $iCount);
		}
		return array(
			'collection' => $aData,
			'count' => count($aData),
			/*
			 * фикс бага с запятой вместо точки из-за установок локали
			 */
			'total_price' => number_format($fTotalPrice, 2, '.', ''),
		);
	}


	/**
	 * Получить массив ид продуктов и их количества в корзине текущего пользователя
	 *
	 * @return array		ключ = ид продукта, значение = количество продуктов
	 */
	public function GetProductIdsFromCart() {
		return isset($_SESSION[self::SESSION_CART_PRODUCTS_KEY]) ? $_SESSION[self::SESSION_CART_PRODUCTS_KEY] : array();
	}


	/**
	 * Задать массив ид продуктов и их количества в корзине
	 *
	 * @param $aData
	 */
	public function SetProductsToCart($aData) {
		$_SESSION[self::SESSION_CART_PRODUCTS_KEY] = $aData;
	}


	/**
	 * Очистить корзину пользователя
	 */
	public function ResetCart() {
		unset($_SESSION[self::SESSION_CART_PRODUCTS_KEY]);
	}


	/*
	 *
	 * --- Методы ОРМ ---
	 *
	 */

	/**
	 * Получить заказ по ид
	 *
	 * @param $iId				ид заказа
	 * @return mixed
	 */
	public function MyGetOrderById($iId) {
		return $this->GetOrderById($iId);
	}


	/**
	 * Получить все заказы
	 *
	 * @return mixed
	 */
	public function MyGetOrderItemsAll() {
		return $this->GetOrderItemsAll(array('#order' => $this->aDefaultSortingOrder));
	}


	/**
	 * Получить количество заказов по новизне заказа и айпи
	 *
	 * @param $iNew			статус новизны заказа
	 * @param $sIp			айпи
	 * @return int
	 */
	protected function MyGetCountItemsByNewAndIp($iNew, $sIp) {
		return $this->GetCountItemsByFilter(array(
			'new' => $iNew,
			'ip' => $sIp,
		), 'Order');
	}


	/**
	 * Достигнут ли лимит новых заказов для айпи
	 *
	 * @param $sIp			айпи
	 * @return bool
	 */
	public function GetNewOrdersLimitExceedByIp($sIp) {
		return $this->MyGetCountItemsByNewAndIp(PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED, $sIp) >= Config::Get('plugin.simplecatalog.product.shop.max_orders_count_per_ip');
	}


}

?>