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
 * Хелперы для обработки меток на карте (карты Яндекса)
 *
 * Этот редактор был написан для плагина Simplecatalog и является его частью и не может быть использован в сторонних продуктах.
 *
 */

var ls = ls || {};

ls.sc_map_helpers_yandex = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * ид враппера, содержащего инпуты с данными о метках
		 */
		map_items_wrapper_id: 'sc_product_add_map_items_wrapper',

		/**
		 * последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Создает при обновлении карты скрытые инпуты с данными всех меток на карте
	 *
	 * @constructor
	 */
	this.ItemsChangeHandler = function() {
		var oMapItemsWrapper = $ ('#' + this.selectors.map_items_wrapper_id);
		oMapItemsWrapper.empty();
		/**
		 * по всем меткам
		 */
		ls.sc_map_editor_yandex.GetGeoObjects().each(function (el, i) {
			/**
			 * получать только точки (прямые геообъекты)
			 * tip: в геобъектах карты могут быть коллекции (например, коллекция с геобъектом текущих координат пользователя)
			 */
			if (!(el instanceof ymaps.GeoObject)) {
				return ;
			}

			var aCoords = el.geometry.getCoordinates(),
				sName = el.properties.get('iconContent'),
				sHint = el.properties.get('hintContent'),
				sContent = el.properties.get('balloonContent'),
				sPreset = el.options.get('preset');

			oMapItemsWrapper.append($ ('<input />', {
				type: 'hidden',
				name: 'product_map_items[id][]',
				value: el.id
			})).append($ ('<input />', {
				type: 'hidden',
				name: 'product_map_items[coord_lat][]',
				value: parseFloat(aCoords[0]).toPrecision(9)			// 2+7
			})).append($ ('<input />', {
				type: 'hidden',
				name: 'product_map_items[coord_lng][]',
				value: parseFloat(aCoords[1]).toPrecision(9)
			})).append($ ('<input />', {
				type: 'hidden',
				name: 'product_map_items[name][]',
				value: sName
			})).append($ ('<input />', {
				type: 'hidden',
				name: 'product_map_items[hint][]',
				value: sHint
			})).append($ ('<input />', {
				type: 'hidden',
				name: 'product_map_items[content][]',
				value: sContent
			})).append($ ('<input />', {
				type: 'hidden',
				name: 'product_map_items[preset][]',
				value: sPreset
			}));
		});
	};


	/**
	 * Вызывается при превышении лимита точек на карту
	 *
	 * @constructor
	 */
	this.ItemsLimitExceedHandler = function() {
		ls.msg.error('', ls.lang.get('plugin.simplecatalog.Errors.map_items.limit_reached', { count: ls.sc_map_editor_yandex.aOptions.iMaxItemsCount }));
	};


	/**
	 * Загрузить точки на карту из враппера инпутов с данными о точках
	 *
	 * @constructor
	 */
	this.LoadItemsIntoMapFromInputsWrapper = function() {
		var oMapItemsWrapper = $ ('#' + this.selectors.map_items_wrapper_id),
			aIds = oMapItemsWrapper.find('input[name="product_map_items[id][]"]'),
			aCoordsLat = oMapItemsWrapper.find('input[name="product_map_items[coord_lat][]"]'),
			aCoordsLng = oMapItemsWrapper.find('input[name="product_map_items[coord_lng][]"]'),
			aTitles = oMapItemsWrapper.find('input[name="product_map_items[name][]"]'),
			aHints = oMapItemsWrapper.find('input[name="product_map_items[hint][]"]'),
			aContents = oMapItemsWrapper.find('input[name="product_map_items[content][]"]'),
			aPresets = oMapItemsWrapper.find('input[name="product_map_items[preset][]"]');
		/**
		 * загрузить точки по данным инпутов
		 */
		for (var i = 0; i < aIds.length; i ++) {
			ls.sc_map_editor_yandex.AddNewPlaceholder({
				id: $ (aIds[i]).val(),
				coords: [$ (aCoordsLat[i]).val(), $ (aCoordsLng[i]).val()],
				title: $ (aTitles[i]).val(),
				hint: $ (aHints[i]).val(),
				content: $ (aContents[i]).val(),
				preset: $ (aPresets[i]).val()
			});
		}
	};

	// ---

	return this;

}).call (ls.sc_map_helpers_yandex || {}, jQuery);
