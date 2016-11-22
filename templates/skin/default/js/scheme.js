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

ls.simplecatalog_scheme = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * Тело таблицы схем
		 */
		schemes_table_body: 'div.Simplecatalog.Scheme.Index .table-items-list tbody',

		/**
		 * Последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Установить слушатель сортировки схем
	 *
	 * @constructor
	 */
	this.AssignListenerForSortingSchemes = function () {
		/**
		 * docs: http://api.jqueryui.com/sortable/
		 * Note: In order to sort table rows, the tbody must be made sortable, not the table
		 */
		$ (this.selectors.schemes_table_body).sortable({
			cursor: 'move',
			helper: ls.simplecatalog.SortableCellsWidthHelper,
			update: function(event, ui) {
				/**
				 * получить ид в порядке расположения
				 */
				var aIds = ls.simplecatalog.GetItemsIdsOrder(ls.simplecatalog_scheme.selectors.schemes_table_body + ' > *', 'data-item-id');
				/**
				 * сохранить новую сортировку
				 */
				ls.ajax(
					aRouter['scheme'] + 'ajax-change-sorting-order',
					{
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
	
}).call (ls.simplecatalog_scheme || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * Установить слушатель сортировки схем
	 */
	ls.simplecatalog_scheme.AssignListenerForSortingSchemes();
	
});
