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

ls.product_shop = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * кнопка "купить"
		 */
		buy_button: '.js-product-buy-button',


		/**
		 * родительский элемент кнопки "купить" и поля количества
		 */
		count_field_wrapper: '.js-product-count-field-wrapper',
		/**
		 * поле количества продуктов для покупки
		 */
		count_field: '.js-product-count-field',
		/**
		 * кнопка уменьшения количества продуктов в поле возле кнопки "купить"
		 */
		count_field_minus: '.js-product-count-field-minus',
		/**
		 * кнопка увеличения количества продуктов в поле возле кнопки "купить"
		 */
		count_field_plus: '.js-product-count-field-plus',


		/**
		 * враппер списка элементов корзины
		 */
		cart_items_wrapper: '#js-sc-cart-items-wrapper',
		/**
		 * редактируемое количество продуктов в корзине
		 */
		cart_items_count_input_change: '.js-sc-cart-count-change',
		/**
		 * кнопка удаления продукта из корзины
		 */
		remove_cart_item: '.js-sc-cart-remove-item',
		/**
		 * ссылка в тулбаре для показа корзины
		 */
		show_cart_button: '.js-sc-show-cart',
		/**
		 * блок в тулбаре с ссылкой для показа корзины
		 */
		toolbar_section: '.js-sc-toolbar-button',


		/*
			последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Получить объект поля количества продуктов возле кнопки "купить" (под общим враппером)
	 *
	 * @param oThis				соседний объект под тем же враппером
	 * @returns {*|jQuery}
	 * @constructor
	 */
	this.GetProductsCountField = function(oThis) {
		return $ (oThis).closest(this.selectors.count_field_wrapper).find(this.selectors.count_field);
	};


	/**
	 * Установить слушатель нажатия кнопки "купить"
	 *
	 * @constructor
	 */
	this.AssignListenerForBuyButton = function() {
		$ (this.selectors.buy_button).click(function () {
			ls.ajax(
				aRouter['shop'] + 'cart/ajax-add-to-cart',
				{
					product_id: $ (this).attr('data-product-id'),
					count: ls.product_shop.GetProductsCountField(this).val()
				},
				function(data) {
					if (!data) {
						ls.msg.error('Error', 'Please, try again later');
					} else {
						if (data.bStateError) {
							ls.msg.error(data.sMsgTitle, data.sMsg);
						} else {
							/**
							 * добавить список продуктов
							 */
							$ (ls.product_shop.selectors.cart_items_wrapper).html(data.sText);
							/**
							 * показать модальное окно
							 * tip: в новой версии лс нужно будет переделать это т.к. используются другие модальные окна
							 */
							$ ('#js-sc-cart').modal('show');

							/**
							 * обновить кнопку в тулбаре
							 */
							ls.product_shop.ShowToolbarSection(data.bEmptyCart);

							ls.msg.notice(data.sMsgTitle, data.sMsg);
						}
					}
				}
			);
			return false;
		});
	};


	/**
	 * Установить слушатель изменения количества продуктов в корзине
	 *
	 * @constructor
	 */
	this.AssignListenerForChangeProductCountInCart = function() {
		$ (document).on('change.sc', this.selectors.cart_items_count_input_change, function () {
			/**
			 * отключить все поля ввода до обновления
			 */
			$ (ls.product_shop.selectors.cart_items_count_input_change).attr('readonly', true).attr('disabled', true);
			$ (ls.product_shop.selectors.cart_items_wrapper).toggleClass('loading');
			ls.ajax(
				aRouter['shop'] + 'cart/ajax-add-to-cart',
				{
					product_id: $ (this).attr('data-product-id'),
					count: $ (this).val()
				},
				function(data) {
					if (!data) {
						ls.msg.error('Error', 'Please, try again later');
					} else {
						if (data.bStateError) {
							ls.msg.error(data.sMsgTitle, data.sMsg);
						} else {
							/**
							 * обновить список продуктов
							 */
							$ (ls.product_shop.selectors.cart_items_wrapper).html(data.sText).toggleClass('loading');

							ls.msg.notice(data.sMsgTitle, data.sMsg);
						}
					}
				}
			);
		});
	};


	/**
	 * Установить слушатель удаления продукта из корзины
	 *
	 * @constructor
	 */
	this.AssignListenerForRemoveProductFromCart = function() {
		$ (document).on('click.sc', this.selectors.remove_cart_item, function () {
			/**
			 * отключить все поля ввода до обновления
			 */
			$ (ls.product_shop.selectors.cart_items_count_input_change).attr('readonly', true).attr('disabled', true);
			$ (ls.product_shop.selectors.cart_items_wrapper).toggleClass('loading');
			ls.ajax(
				aRouter['shop'] + 'cart/ajax-remove-cart-item',
				{
					product_id: $ (this).attr('data-product-id')
				},
				function(data) {
					if (!data) {
						ls.msg.error('Error', 'Please, try again later');
					} else {
						if (data.bStateError) {
							ls.msg.error(data.sMsgTitle, data.sMsg);
						} else {
							/**
							 * обновить список продуктов
							 */
							$ (ls.product_shop.selectors.cart_items_wrapper).html(data.sText).toggleClass('loading');

							/**
							 * обновить кнопку в тулбаре
							 */
							ls.product_shop.ShowToolbarSection(data.bEmptyCart);

							ls.msg.notice(data.sMsgTitle, data.sMsg);
						}
					}
				}
			);
			return false;
		});
	};


	/**
	 * Установить слушатель нажатия кнопки отображения корзины
	 *
	 * @constructor
	 */
	this.AssignListenerForShowCartButton = function() {
		$ (this.selectors.show_cart_button).click(function () {
			ls.ajax(
				aRouter['shop'] + 'cart/ajax-get-cart-items',
				{},
				function(data) {
					if (!data) {
						ls.msg.error('Error', 'Please, try again later');
					} else {
						if (data.bStateError) {
							ls.msg.error(data.sMsgTitle, data.sMsg);
						} else {
							/**
							 * добавить список продуктов
							 */
							$ (ls.product_shop.selectors.cart_items_wrapper).html(data.sText);
							/**
							 * показать модальное окно
							 * tip: в новой версии лс нужно будет переделать это т.к. используются другие модальные окна
							 */
							$ ('#js-sc-cart').modal('show');

							ls.msg.notice(data.sMsgTitle, data.sMsg);
						}
					}
				}
			);
			return false;
		});
	};


	/**
	 * Показать или спрятать блок в тулбаре с кнопкой для доступа к корзине
	 *
	 * @constructor
	 */
	this.ShowToolbarSection = function(bEmptyCart) {
		var oToolbarSection = $ (this.selectors.toolbar_section);
		if (bEmptyCart) {
			oToolbarSection.hide(100);
		} else {
			oToolbarSection.show(100);
		}
	};


	/**
	 * Установить слушатели кнопок изменения количества продуктов (возле кнопки "купить") и проверку изменения поля количества вручную
	 *
	 * @constructor
	 */
	this.AssignListenersForCountFieldActionsButtons = function() {
		/**
		 * кнопка уменьшения количества
		 */
		$ (document).on('click.sc', this.selectors.count_field_minus, function () {
			var oField = ls.product_shop.GetProductsCountField(this);
			var iValueNew = parseInt(oField.val()) - 1;
			oField.val(iValueNew > 0 ? iValueNew : 1);
		});
		/**
		 * кнопка увеличения количества
		 */
		$ (document).on('click.sc', this.selectors.count_field_plus, function () {
			var oField = ls.product_shop.GetProductsCountField(this);
			var iValueNew = parseInt(oField.val()) + 1;
			oField.val(iValueNew < 99 ? iValueNew : 99);
		});
		/**
		 * проверять вводимые значения в полях количества продуктов
		 */
		$ (document).on('change.sc', this.selectors.count_field, function () {
			var mValue = parseInt($ (this).val());
			if (isNaN(mValue) || !mValue) {
				mValue = 1;
			}
			$ (this).val(mValue);
		});
	};

	// ---

	return this;

}).call (ls.product_shop || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * установить слушатель кнопки покупки продукта
	 */
	ls.product_shop.AssignListenerForBuyButton();

	/**
	 * установить слушатель изменения количества продуктов в корзине
	 */
	ls.product_shop.AssignListenerForChangeProductCountInCart();

	/**
	 * установить слушатель удаление продукта из корзины
	 */
	ls.product_shop.AssignListenerForRemoveProductFromCart();

	/**
	 * установить слушатель кнопки отображения корзины
	 */
	ls.product_shop.AssignListenerForShowCartButton();

	/**
	 * установить слушатели кнопок изменения количества продуктов (возле кнопки "купить") и проверку изменения поля количества вручную
	 */
	ls.product_shop.AssignListenersForCountFieldActionsButtons();

});
