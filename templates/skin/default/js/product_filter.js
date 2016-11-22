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

var ls = ls || {};

ls.sc_product_filter = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/*
			показать/скрыть фильтр продуктов в сайдбаре
		 */
		toggle_product_filter: 'div.Simplecatalog .js-toggle-product-filter',
		product_filter_wrapper: 'div.Simplecatalog .js-product-filter-wrapper',

		/*
			для удобства
		 */
		last_comma: true
	};


	/**
	 * Добавить слайдер
	 *
	 * @param sContainerClass		класс контейнера
	 * @param iRangeMin				мин. значение (всего)
	 * @param iRangeMax				макс. значение (всего)
	 * @param iStartMin				мин. текущее значение
	 * @param iStartMax				макс. текущее значение
	 * @param iNumberResolution		точность
	 * @param sFieldClassMin		класс поля для левого ползунка
	 * @param sFieldClassMax		класс поля для правого ползунка
	 * @constructor
	 */
	this.AddSlider = function (sContainerClass, iRangeMin, iRangeMax, iStartMin, iStartMax, iNumberResolution, sFieldClassMin, sFieldClassMax) {
		// docs: http://refreshless.com/nouislider/slider-options
		$ (sContainerClass).noUiSlider({
			range: [iRangeMin, iRangeMax],
			start: [iStartMin, iStartMax],
			handles: 2,
			connect: true,
			behaviour: 'tap-drag',
			serialization: {
				mark: '.',
				resolution: iNumberResolution,
				to: [
					$ (sFieldClassMin),
					$ (sFieldClassMax)
				]
			}
		});
	};
	
	// ---

	return this;
	
}).call (ls.sc_product_filter || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/*
		скрыть/показать фильтр продуктов в сайдбаре
	 */
	$ (ls.sc_product_filter.selectors.toggle_product_filter).click(function() {
		$ (ls.sc_product_filter.selectors.product_filter_wrapper).slideToggle(250);
		return false;
	});

});
