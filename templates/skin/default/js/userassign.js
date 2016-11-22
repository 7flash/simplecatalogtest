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

ls.simplecatalog_userassign = (function ($) {

	this.newFunction = function () {

	};
	
	// ---

	return this;
	
}).call (ls.simplecatalog_userassign || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * Автокомплитер для логинов пользователей
	 */
	ls.autocomplete.add ($ ('.SC_AC_Multi_Logins'), aRouter ['ajax'] + 'autocompleter/user/', true);

});
