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

/**
 *
 * Сравнение продуктов
 *
 */

var ls = ls || {};

ls.product_compare = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * ссылки для добавления к сравнению продуктов
		 */
		compare: {
			links_wrapper: '.js-sc-compare-link-wrapper',
			links_class: '.js-sc-compare-products-link.active'
		},
		/**
		 * страница сравнения продуктов (таблица)
		 */
		compare_table: {
			show_all: '#js_sc_compare_products_show_all',
			show_different: '#js_sc_compare_products_show_different',
			comparing_table: '#js_sc_compare_products_table'
		},


		/**
		 * последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Слушатель события нажатия ссылок для добавления продукта к сравнению
	 *
	 * @constructor
	 */
	this.AssignListenersForCompareButtons = function () {
		$ (this.selectors.compare.links_class).bind('click.sc', function() {
			var oLink = this;
			ls.ajax(
				aRouter['product'] + 'compare/add',
				{
					id: $ (this).attr('data-product-id')
				},
				function(data) {
					if (!data) {
						ls.msg.error('Error', 'Please, try again later');
					} else {
						if (data.bStateError) {
							ls.msg.error(data.sMsgTitle, data.sMsg);
						} else {
							ls.product_compare.CompareAjaxRequestResponseHandler(data, oLink);
						}
					}
				}
			);
			return false;
		});
	};


	/**
	 * Хендлер ответа от сервера на сравнение продуктов
	 *
	 * @param data		объект ответа
	 * @param oLink		объект кнопки
	 * @constructor
	 */
	this.CompareAjaxRequestResponseHandler = function (data, oLink) {
		/*
		 	первый добавленный продукт - вывести надпись
		 */
		if (data.bFirst) {
			$ (oLink).closest(this.selectors.compare.links_wrapper).html(data.sText);
		} else if (data.aProductsIds) {
			/*
				фикс, когда список id приходит как объект (если ключи в массиве не начинаются с 0, а после удаления из списка сравнения так и есть)
			 */
			if (typeof data.aProductsIds == 'object') {
				var aIds = [];
				for (var key in data.aProductsIds) {
					aIds.push(data.aProductsIds[key]);
				}
			} else {
				aIds = data.aProductsIds;
			}
			/*
				есть списки ид продуктов, для которых нужно построить ссылку
			 */
			$ (oLink).attr('href', aRouter['product'] + 'compare/' + aIds.join('/')).removeClass('active').unbind('click.sc').html(data.sText);
			ls.msg.notice('Ok');
		}
	};


	/**
	 * События для нажатий на кнопки показа/скрытия одинаковых полей таблицы сравнения
	 *
	 * @constructor
	 */
	this.AssignListenersForShowComparingFields = function() {
		$ (this.selectors.compare_table.show_all).bind('click.sc', function() {
			$ (ls.product_compare.selectors.compare_table.comparing_table + ' tr.equal').show(150);
			$ (this).closest('ul').children().removeClass('active');
			$ (this).closest('li').addClass('active');
			return false;
		});
		$ (this.selectors.compare_table.show_different).bind('click.sc', function() {
			$ (ls.product_compare.selectors.compare_table.comparing_table + ' tr.equal').hide(150);
			$ (this).closest('ul').children().removeClass('active');
			$ (this).closest('li').addClass('active');
			return false;
		});
	};
	
	// ---

	return this;
	
}).call (ls.product_compare || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * привязать событие нажатия на кнопку сравнения продуктов
	 */
	ls.product_compare.AssignListenersForCompareButtons();

	/**
	 * привязать событие нажатия на кнопки показа/скрытия полей в таблице
	 */
	ls.product_compare.AssignListenersForShowComparingFields();

});
