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
 * Обработка ембед кода для вставки карточки продукта в блоги
 *
 */

var ls = ls || {};

ls.simplecatalog_product_embed_code = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * Ссылка для вызова модального окна с ембед кодом продукта
		 */
		get_product_embed_code_link: '.js-sc-get-product-embed-code',
		/**
		 * textarea для вывода ембед кода
		 */
		embed_code_wrapper: '#js-sc-embed-code-wrapper',
		/**
		 * Обертка для инжекта ембед кода для предпросмотра
		 */
		embed_code_live_preview: '#js-sc-embed-live-preview',


		/**
		 * Последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Получить ембед код для отображения карточки продукта по ид продукта
	 *
	 * @param iProductId		ид продукта
	 * @returns {string}
	 * @constructor
	 */
	this.GetProductEmbedCode = function(iProductId) {
		return '<iframe src="' + aRouter['product'] + 'api/embed/' + iProductId + '/" height="170" width="570" style="border:none;margin:0;padding:0;"></iframe>';
	};


	/**
	 * Установить слушатель нажатия кнопки "вставить в блог" для вывода модального окна с кодом вставки
	 *
	 * @constructor
	 */
	this.AssignListenerForGetEmbedCodeLink = function() {
		$ (this.selectors.get_product_embed_code_link).click(function() {
			/**
			 * получить ембед код по ид продукта
			 */
			var sHtmlCode = ls.simplecatalog_product_embed_code.GetProductEmbedCode($ (this).attr('data-product-id'));
			/**
			 * добавить текст кода в поле ввода
			 */
			$ (ls.simplecatalog_product_embed_code.selectors.embed_code_wrapper).text(sHtmlCode);
			/**
			 * инжект кода в страницу для предпросмотра
			 */
			$ (ls.simplecatalog_product_embed_code.selectors.embed_code_live_preview).html(sHtmlCode);
			/**
			 * показать модальное окно
			 * tip: в новой версии лс нужно будет переделать это т.к. используются другие модальные окна
			 */
			$ ('#js-sc-embed-code-modal').jqmShow();
			return false;
		});
	};

	// ---

	return this;
	
}).call (ls.simplecatalog_product_embed_code || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * установить слушатель нажатия кнопки "вставить в блог" для получения кода
	 */
	ls.simplecatalog_product_embed_code.AssignListenerForGetEmbedCodeLink();

});
