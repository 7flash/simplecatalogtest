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
 * Редактирование продуктов
 *
 */

var ls = ls || {};

ls.product_edit = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * Удаление загруженного файла (при редактировании продукта)
		 */
		remove_uploaded_file_link: '.js-sc-remove-product-field-file-value',
		current_controls_wrapper: '.js-sc-one-uploaded-file-controls-wrapper',

		/**
		 * Проверка типа файла и размера при выборе
		 */
		file_field_check: '.js-sc-file-field-check',


		/**
		 * Последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Установить слушатель кнопки очистки поля файла
	 *
	 * @constructor
	 */
	this.AssignListenersForCleanUploadedFileButton = function() {
		$ (this.selectors.remove_uploaded_file_link).click(function() {
			if (!confirm (ls.lang.get('plugin.simplecatalog.Products.Add.fields.file.delete_file'))) return false;

			var oLink = this;
			ls.ajax(
				aRouter['product'] + 'edit/ajax-clean-file-field',
				{
					product_id: $ (this).attr('data-product-id'),
					scheme_field_id: $ (this).attr('data-scheme-field-id')
				},
				function(data) {
					if (!data) {
						ls.msg.error('Error', 'Please, try again later');
					} else {
						if (data.bStateError) {
							ls.msg.error(data.sMsgTitle, data.sMsg);
						} else {
							/**
							 * удалить блок управления загруженным файлом
							 */
							$ (oLink).closest(ls.product_edit.selectors.current_controls_wrapper).remove();
							ls.msg.notice(data.sMsgTitle, data.sMsg);
						}
					}
				}
			);
			return false;
		});
	};


	/**
	 * Установить слушатель выбора файла для проверки расширения и размера файла
	 *
	 * @constructor
	 */
	this.AssignListenersForFileInputChange = function() {
		$ (this.selectors.file_field_check).change(function() {
			/**
			 * очистить отображаемое имя
			 */
			var oDisplayFilename = $ (this).parent().prev();
			oDisplayFilename.html(oDisplayFilename.attr('data-default-html'));

			var sValue = $ (this).val();
			var aExtensionMatch = sValue.match(/\.([^\.]+)$/);
			var sExtension = aExtensionMatch ? aExtensionMatch[1].toLowerCase() : '';
			var aAllowedExtensions = $ (this).attr('data-allowed-extensions').replace(/ /g, '').split(',');
			/**
			 * разрешено ли такое расширение
			 */
			if ($.inArray(sExtension, aAllowedExtensions) === -1) {
				ls.msg.error('', ls.lang.get('plugin.simplecatalog.Errors.files.file_extension_not_allowed', {extension: sExtension, file: sValue}));
				this.value = '';
				return false;
			}
			/**
			 * разрешен ли размер файла (проверки в байтах)
			 */
			if (this.files) {
				var iSize = this.files[0].size;
				var iSizeAllowed = $ (this).attr('data-max-size');
				if (iSize && iSize > iSizeAllowed) {
					ls.msg.error('', ls.lang.get('plugin.simplecatalog.Errors.files.file_size_too_large', {
						current_size: parseInt(iSize/1000),
						max_size: parseInt(iSizeAllowed/1000),
						file: sValue
					}));
					this.value = '';
					return false;
				}
			}
			/**
			 * установить отображаемое имя
			 */
			$ (this).parent().prev().html($ (this).val());
		});
	};

	// ---

	return this;
	
}).call (ls.product_edit || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * установить слушатель кнопки очистки поля файла
	 */
	ls.product_edit.AssignListenersForCleanUploadedFileButton();

	/**
	 * установить слушатель выбора файла
	 */
	ls.product_edit.AssignListenersForFileInputChange();

});
