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
 * Список пресетов для меток на карте (карты Яндекса)
 *
 * Этот редактор был написан для плагина Simplecatalog и является его частью и не может быть использован в сторонних продуктах.
 *
 */

var ls = ls || {};

ls.sc_map_yandex_preset_storage = (function ($) {

	/**
	 * Список всех возможных типов внешнего вида меток для карт Яндекса
	 * docs: http://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage.xml
	 */
	this.aPresets = [
		/**
		 * Метки с текстом
		 */
		[
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blueIcon.png', value: 'islands#blueIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkGreenIcon.png', value: 'islands#darkGreenIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/redIcon.png', value: 'islands#redIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/violetIcon.png', value: 'islands#violetIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkOrangeIcon.png', value: 'islands#darkOrangeIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blackIcon.png', value: 'islands#blackIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/nightIcon.png', value: 'islands#nightIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/yellowIcon.png', value: 'islands#yellowIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkBlueIcon.png', value: 'islands#darkBlueIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/greenIcon.png', value: 'islands#greenIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/pinkIcon.png', value: 'islands#pinkIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/orangeIcon.png', value: 'islands#orangeIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/grayIcon.png', value: 'islands#grayIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/lightBlueIcon.png', value: 'islands#lightBlueIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/brownIcon.png', value: 'islands#brownIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/oliveIcon.png', value: 'islands#oliveIcon'}
		],
		/**
		 * Метки с текстом (иконки тянутся под контент)
		 */
		[
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blueStr.png', value: 'islands#blueStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkGreenStr.png', value: 'islands#darkGreenStretchyIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/redStr.png', value: 'islands#redStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/violetStr.png', value: 'islands#violetStretchyIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkOrangeStr.png', value: 'islands#darkOrangeStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blackStr.png', value: 'islands#blackStretchyIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/nightStr.png', value: 'islands#nightStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/yellowStr.png', value: 'islands#yellowStretchyIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkBlueStr.png', value: 'islands#darkBlueStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/greenStr.png', value: 'islands#greenStretchyIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/pinkStr.png', value: 'islands#pinkStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/orangeStr.png', value: 'islands#orangeStretchyIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/grayStr.png', value: 'islands#grayStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/lightBlueStr.png', value: 'islands#lightBlueStretchyIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/brownStr.png', value: 'islands#brownStretchyIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/oliveStr.png', value: 'islands#oliveStretchyIcon'}
		],
		/**
		 * Метки без содержимого с точкой в центре
		 */
		[
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blueDotIcon.png', value: 'islands#blueDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkGreenDotIcon.png', value: 'islands#darkGreenDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/redDotIcon.png', value: 'islands#redDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/violetDotIcon.png', value: 'islands#violetDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkOrangeDotIcon.png', value: 'islands#darkOrangeDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blackDotIcon.png', value: 'islands#blackDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/nightDotIcon.png', value: 'islands#nightDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/yellowDotIcon.png', value: 'islands#yellowDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkBlueDotIcon.png', value: 'islands#darkBlueDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/greenDotIcon.png', value: 'islands#greenDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/pinkDotIcon.png', value: 'islands#pinkDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/orangeDotIcon.png', value: 'islands#orangeDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/grayDotIcon.png', value: 'islands#grayDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/lightBlueDotIcon.png', value: 'islands#lightBlueDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/brownDotIcon.png', value: 'islands#brownDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/oliveDotIcon.png', value: 'islands#oliveDotIcon'}
		],
		/**
		 * Метки в виде кругов
		 */
		[
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blueCircleIcon.png', value: 'islands#blueCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkGreenCircleIcon.png', value: 'islands#darkGreenCircleIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/redCircleIcon.png', value: 'islands#redCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/violetCircleIcon.png', value: 'islands#violetCircleIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkOrangeCircleIcon.png', value: 'islands#darkOrangeCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blackCircleIcon.png', value: 'islands#blackCircleIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/nightCircleIcon.png', value: 'islands#nightCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/yellowCircleIcon.png', value: 'islands#yellowCircleIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkBlueCircleIcon.png', value: 'islands#darkBlueCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/greenCircleIcon.png', value: 'islands#greenCircleIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/pinkCircleIcon.png', value: 'islands#pinkCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/orangeCircleIcon.png', value: 'islands#orangeCircleIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/grayCircleIcon.png', value: 'islands#grayCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/lightBlueCircleIcon.png', value: 'islands#lightBlueCircleIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/brownCircleIcon.png', value: 'islands#brownCircleIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/oliveCircleIcon.png', value: 'islands#oliveCircleIcon'}
		],
		/**
		 * Метки в виде кругов с точкой в центре
		 */
		[
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blueCircleDotIcon.png', value: 'islands#blueCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkGreenCircleDotIcon.png', value: 'islands#darkGreenCircleDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/redCircleDotIcon.png', value: 'islands#redCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/violetCircleDotIcon.png', value: 'islands#violetCircleDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkOrangeCircleDotIcon.png', value: 'islands#darkOrangeCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/blackCircleDotIcon.png', value: 'islands#blackCircleDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/nightCircleDotIcon.png', value: 'islands#nightCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/yellowCircleDotIcon.png', value: 'islands#yellowCircleDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/darkBlueCircleDotIcon.png', value: 'islands#darkBlueCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/greenCircleDotIcon.png', value: 'islands#greenCircleDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/pinkCircleDotIcon.png', value: 'islands#pinkCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/orangeCircleDotIcon.png', value: 'islands#orangeCircleDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/grayCircleDotIcon.png', value: 'islands#grayCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/lightBlueCircleDotIcon.png', value: 'islands#lightBlueCircleDotIcon'},

			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/brownCircleDotIcon.png', value: 'islands#brownCircleDotIcon'},
			{image: 'http://api.yandex.ru/maps/doc/jsapi/2.1/ref/images/styles/oliveCircleDotIcon.png', value: 'islands#oliveCircleDotIcon'}
		]
	];

	// ---

	return this;

}).call (ls.sc_map_yandex_preset_storage || {}, jQuery);
