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
	Магазин
 */

var ls = ls || {};

ls.product_shop_order = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * ссылка раскрытия формы комментария
		 */
		add_comment: '.js-sc-order-add-comment',
		/**
		 * контейнер комментария
		 */
		comment_wrapper: '.js-sc-order-comment',


		/**
		 * селект выбора типа доставки
		 */
		delivery_select: '.js-sc-order-delivery-select',
		/**
		 * контейнер имени получателя
		 */
		receiver_name: '.js-sc-order-receiver-name',
		/**
		 * контейнер гео-данных
		 */
		adress_data: '.js-sc-order-adress-data',


		/**
		 * последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Установить слушатель нажатия кнопки раскрытия комментария
	 *
	 * @constructor
	 */
	this.AssignListenerForAddCommentLink = function() {
		$ (this.selectors.add_comment).click(function () {
			$ (this).fadeOut(100, function() {
				$ (this).remove();
			});
			$ (ls.product_shop_order.selectors.comment_wrapper).fadeIn(100);
			return false;
		});
	};


	/**
	 * Установить слушатель выбора типа доставки
	 *
	 * @constructor
	 */
	this.AssignListenerForDeliveryTypeSelect = function() {
		$ (this.selectors.delivery_select).change(function () {
			var oSelect = $ (this);
			/**
			 * если доставка через курьера - добавить выбор адреса
			 */
			if (oSelect.val() == 2) {
				$ (ls.product_shop_order.selectors.receiver_name).fadeIn(100);
				$ (ls.product_shop_order.selectors.adress_data).fadeIn(100);
			} else {
				$ (ls.product_shop_order.selectors.receiver_name).fadeOut(100);
				$ (ls.product_shop_order.selectors.adress_data).fadeOut(100);
			}
		});
		$ (this.selectors.delivery_select).trigger('change');
	};

	// ---

	return this;
	
}).call (ls.product_shop_order || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * Установить слушатель нажатия кнопки раскрытия комментария
	 */
	ls.product_shop_order.AssignListenerForAddCommentLink();

	/**
	 * Установить слушатель выбора типа доставки
	 */
	ls.product_shop_order.AssignListenerForDeliveryTypeSelect();

});
