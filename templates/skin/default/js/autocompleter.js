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
 * Расширение стандартных методов автокомплитера jQuery UI
 *
 */

var ls = ls || {};

ls.simplecatalog_autocompleter = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * Класс выводимого в автокомплитере изображения
		 */
		image_class: 'sc-autocompleter-product-image',


		/**
		 * Последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Добавить автокомплитер с выводом изображения элемента
	 *
	 * @param oObj				объект
	 * @param sPath				путь к аякс запросу
	 * @param iMinLength		минимальная длина строки для отправки запроса на сервер
	 * @constructor
	 */
	this.AddAutocompleterWithImagesSupportInItems = function (oObj, sPath, iMinLength) {
		/**
		 * Автокомплитер jQuery UI
		 * docs: http://api.jqueryui.com/autocomplete/
		 */
		oObj.autocomplete({
			source: function(request, response) {
				ls.ajax(
					sPath,
					{
						value: request.term
					},
					function(data) {
						response(data.aItems);
					}
				);
			},
			/**
			 * количество символов для отправки запроса на сервер
			 */
			minLength: iMinLength,
			/**
			 * нужно ли выводить изображение элемента
			 */
			showItemImage: true
		});
	};


	/**
	 * Расширение возможностей автокомплитера
	 *
	 * @constructor
	 */
	this.ExtendAutocompleterPrototype = function () {
		var proto = $.ui.autocomplete.prototype;
		var renderItem = proto._renderItem;
		$.extend(proto, {
			/**
			 * Вывод одного элемента и его изображения
			 */
			_renderItem: function(ul, item) {
				/**
				 * если нужно добавить изображение элемента
				 */
				if (this.options.showItemImage) {
					return $ ('<li/>').data('item.autocomplete', item).append(
						$ ('<a/>').append(
							$ ('<img />', {
								src: item.image,
								class: ls.simplecatalog_autocompleter.selectors.image_class,
								alt: item.label,
								title: item.label
							}),
							item.label
						)
					).appendTo(ul);

				} else {
					/**
					 * вызвать родительский метод для создания элемента
					 */
					return renderItem.apply(this, arguments);
				}
			}
		});
	};

	// ---

	return this;

}).call (ls.simplecatalog_autocompleter || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * Расширение возможностей автокомплитера
	 */
	ls.simplecatalog_autocompleter.ExtendAutocompleterPrototype();

});
