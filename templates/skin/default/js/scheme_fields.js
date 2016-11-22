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

ls.simplecatalog_schemefields = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * Селектор типа поля
		 */
		field_type: 'div.Simplecatalog form select[name^="field_type"]',
		/**
		 * Переключатель отображения полей схемы
		 */
		toggle_field_list: 'div.Simplecatalog.Scheme.Index .js-toggle-field-list',
		/**
		 * Тело таблицы полей схемы
		 */
		scheme_fields_table_body: 'div.Simplecatalog.Field.Index .table-items-list tbody',

		/**
		 * Последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Установить слушатель сортировки полей схемы
	 *
	 * @constructor
	 */
	this.AssignListenerForSortingSchemeFields = function () {
		/**
		 * docs: http://api.jqueryui.com/sortable/
		 * Note: In order to sort table rows, the tbody must be made sortable, not the table
		 */
		$ (this.selectors.scheme_fields_table_body).sortable({
			cursor: 'move',
			helper: ls.simplecatalog.SortableCellsWidthHelper,
			update: function(event, ui) {
				/**
				 * получить ид схемы
				 */
				var iSchemeId = $ (ls.simplecatalog_schemefields.selectors.scheme_fields_table_body).attr('data-scheme-id');
				/**
				 * получить ид в порядке расположения
				 */
				var aIds = ls.simplecatalog.GetItemsIdsOrder(ls.simplecatalog_schemefields.selectors.scheme_fields_table_body + ' > *', 'data-item-id');
				/**
				 * сохранить новую сортировку
				 */
				ls.ajax(
					aRouter['field'] + 'ajax-change-sorting-order',
					{
						scheme_id: iSchemeId,
						ids: aIds
					},
					function(data) {
						if (data.bStateError) {
							ls.msg.error(data.sMsgTitle, data.sMsg);
						} else {
							ls.msg.notice(data.sMsgTitle, data.sMsg);
						}
					}
				);
			}
		}).disableSelection();
	};

	// ---

	return this;
	
}).call (ls.simplecatalog_schemefields || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * Смена типа поля в селекте при редактировании поля и отображение соответствующих доп. полей
	 */
	$ (ls.simplecatalog_schemefields.selectors.field_type).bind ('change.simplecatalog', function () {
		var sClassBegins = 'FieldType_';
		var oParent = $ (this).closest ('form');
		oParent.find ('*[class*="' + sClassBegins + '"]').hide (0);
		oParent.find ('*[class~="' + sClassBegins + $ (this).val () + '"]').show (0);
	});
	$ (ls.simplecatalog_schemefields.selectors.field_type).trigger ('change.simplecatalog');
	
	/**
	 * Показать короткий список полей схемы
	 */
	$ (ls.simplecatalog_schemefields.selectors.toggle_field_list).bind ('click.simplecatalog', function () {
		$ (this).parent ().find ('.js-field-list').slideToggle (150);
		return false;
	});

	/**
	 * Установить слушатель сортировки полей схемы
	 */
	ls.simplecatalog_schemefields.AssignListenerForSortingSchemeFields();

});
