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
 * Общие методы продуктов
 *
 */

var ls = ls || {};

ls.simplecatalog_product = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * Вкладка
		 */
		tab: '.js-sc-tab',
		/**
		 * Контейнер для вкладки
		 */
		tab_content: '.js-sc-tab-content',


		/**
		 * Открывающая кнопка для связанного контейнера
		 */
		toggle_switch_link: '.js-sc-toggle-switch',


		/**
		 * последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Установить слушатель переключения вкладок
	 *
	 * @constructor
	 */
	this.AssignListenersForTabs = function() {
		$ (document).on('click.sc', this.selectors.tab, function () {
			var sGroup = $ (this).attr('data-product-tab-group');
			var iLinkedContentId = $ (this).attr('data-product-tab-id');
			/**
			 * если активна текущая вкладка - не переключать
			 */
			if ($ (this).hasClass('active')) return false;
			/**
			 * обновить классы у вкладок
			 */
			$ (ls.simplecatalog_product.selectors.tab + '[data-product-tab-group="' + sGroup + '"]').removeClass('active');
			$ (this).addClass('active');
			/**
			 * переключить содержимое
			 */
			$ (ls.simplecatalog_product.selectors.tab_content + '[data-product-tab-group="' + sGroup + '"]').toggle(false);
			$ (ls.simplecatalog_product.selectors.tab_content + '[data-product-tab-group="' + sGroup + '"][data-product-tab-id="' + iLinkedContentId + '"]').toggle(true);
			return false;
		});
	};


	/**
	 * Установить слушатель кнопок открывания связанного контейнера
	 *
	 * @constructor
	 */
	this.AssignListenersForToggleSwitch = function() {
		/**
		 * tip: событие ifToggled - изменение состояния флажка от плагина iCheck
		 */
		$ (document).on('click.sc, ifToggled.sc', this.selectors.toggle_switch_link, function () {
			$ (this).toggleClass('active');
			$ ('#' + $ (this).attr('data-linked-id')).slideToggle(100);
			return false;
		});
	};

	// ---

	return this;
	
}).call (ls.simplecatalog_product || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * добавить аякс обработку комментариев продуктов
	 */
	ls.comments.options.type.product = {
		url_add: aRouter['product-comments'] + 'ajax-add-comment/',
		url_response: aRouter['product-comments'] + 'ajax-response-comment/'
	};
	
	/**
	 * tip: онлайн комментарии заданы для каждой схемы в файле block_stream_nav_item.tpl
	 */

	/**
	 * установить слушатель переключения вкладок
	 */
	ls.simplecatalog_product.AssignListenersForTabs();

	/**
	 * установить слушатель кнопок открывания связанного контейнера
	 */
	ls.simplecatalog_product.AssignListenersForToggleSwitch();

});
