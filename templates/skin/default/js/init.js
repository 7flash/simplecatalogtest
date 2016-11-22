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
 * Общие методы
 *
 */

var ls = ls || {};

ls.simplecatalog = (function ($) {

	/**
	 *
	 * --- Для сортировки ---
	 *
	 */

	/**
	 * Хелпер для установки ширины ячеек таблицы при сортировке
	 *
	 * @param e
	 * @param ui
	 * @returns {*}
	 * @constructor
	 */
	this.SortableCellsWidthHelper = function (e, ui) {
		ui.children ().each (function () {
			$ (this).width ($ (this).width ());
		});
		return ui;
	};


	/**
	 * Получить ид элементов в порядке их расположения
	 *
	 * @param sSelector			селектор для отбора элементов
	 * @param sAttrName			имя атрибута элемента с его ид
	 * @returns {Array}
	 * @constructor
	 */
	this.GetItemsIdsOrder = function (sSelector, sAttrName) {
		var aIds = [];
		$ (sSelector).each(function (i, o) {
			var iId = $ (o).attr(sAttrName);
			aIds.push(iId);
		});
		return aIds;
	};
	
	// ---

	return this;
	
}).call (ls.simplecatalog || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * Вопрос-подтверждение при активации элемента
	 */
	$ (document).on('click.simplecatalog', 'div.Simplecatalog .js-question', function() {
		var sMsg = $(this).attr('data-question-title') ? $(this).attr('data-question-title') : null;
		if (!sMsg) {
			sMsg = $(this).attr('title') ? $(this).attr('title') + '?' : 'Ok?';
		}
		if (!confirm(sMsg)) return false;
	});

	/**
	 * Стилизация чекбоксов и радиокнопок
	 */
	$('div.Simplecatalog input[type="checkbox"], div.Simplecatalog input[type="radio"]').iCheck({
		labelHover: false,
		cursor: true,
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'icheckbox_square-green'
	});
	/**
	 * Хак для чекбокса подписки на комментарии продукта (т.к. не вызывается автоматически подписка при клике из-за стилизации)
	 */
	$ ('#comment_subscribe').on('ifChanged.sc', function() {
		eval($ (this).attr('onchange'));
	});

	/**
	 * Выбор даты в формате php
	 * tip: формат даты изменен для удобства работы с ней на стороне php
	 */
	$('.js-date-picker-php').datepicker({
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ls.lang.get('plugin.simplecatalog.datepicker.days'),
		monthNames: ls.lang.get('plugin.simplecatalog.datepicker.months'),
		firstDay: 1
	});

	/**
	 * Переход по ссылке при активации кнопки
	 */
	$ (document).on('click.simplecatalog', '.js-sc-button-url', function() {
		window.location.href = $ (this).attr('data-url');
		return false;
	});

	/**
	 * Переход по ссылке при выборе значения в селекте
	 */
	$ (document).on('change.simplecatalog', '.js-sc-select-url', function() {
		window.location.href = $ (this).attr('value');
	});

});
