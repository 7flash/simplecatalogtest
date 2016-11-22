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

ls.simplecatalog_images = (function ($) {

	/**
	 * Селекторы
	 */
	this.selectors = {
		/**
		 * ид формы загрузки изображений
		 */
		images_upload_form: '#js-sc-images-upload-form',
		/**
		 * инпут загрузки изображений
		 */
		image_upload_input: '.js-sc-images-upload-input',
		/**
		 * список загруженных изображений
		 */
		uploaded_images_list: '#js-sc-images-uploaded-list',
		/**
		 * удаление загруженного изображения
		 */
		delete_uploaded_image: '#js-sc-images-upload-form .js-remove-image',

		/**
		 * последний элемент без запятой, для удобства
		 */
		last_element: true
	};


	/**
	 * Установить слушатель выбора файла и сабмита формы
	 *
	 * @constructor
	 */
	this.AssignListenerForImagesUpload = function () {
		/**
		 * слушатель сабмита формы
		 */
		$ (this.selectors.images_upload_form).ajaxForm({
			url: aRouter['sc_images'] + 'ajax-upload-images',
			dataType: 'json',
			beforeSend: function() {
				$ (ls.simplecatalog_images.selectors.images_upload_form).toggleClass('loading');
			},
			success: function(data) {
				if (data.bStateError) {
					ls.msg.error(data.sMsgTitle, data.sMsg);
				} else {
					ls.msg.notice(data.sMsgTitle, data.sMsg);
					/**
					 * добавить хтмл код загруженных изображений
					 */
					$ (ls.simplecatalog_images.selectors.uploaded_images_list).prepend(data.sText);
				}
			},
			complete: function(xhr) {
				$ (ls.simplecatalog_images.selectors.images_upload_form).toggleClass('loading');
			}
		});

		/**
		 * слушатель изменения файла (после корректной проверки сабмитит форму)
		 */
		$ (this.selectors.image_upload_input).change(function() {
			var iAlreadyUploadedImages = $ (ls.simplecatalog_images.selectors.uploaded_images_list).find('img').length;
			var iFilesToUpload = this.files.length;
			var iMaxAllowedCount = $ (this).attr('data-max-images-count');
			/**
			 * проверить количество загружаемых изображений
			 */
			if (iAlreadyUploadedImages + iFilesToUpload > iMaxAllowedCount) {
				ls.msg.error('', ls.lang.get('plugin.simplecatalog.Errors.image_upload.max_count_exceed', {
					max_count: iMaxAllowedCount,
					current_count: iAlreadyUploadedImages,
					try_count: iFilesToUpload
				}));
				this.value = '';
				return false;
			}
			/**
			 * проверить все выбранные файлы
			 */
			var aAllowedExtensions = $ (this).attr('data-allowed-extensions').replace(/ /g, '').split(',');
			var iMaxImageFileSizeAllowed = $ (this).attr('data-max-image-file-size');
			var bSuccess = true;
			$ (this.files).each(function(i, o) {
				var sValue = o.name;
				var aExtensionMatch = sValue.match(/\.([^\.]+)$/);
				var sExtension = aExtensionMatch ? aExtensionMatch[1].toLowerCase() : '';
				/**
				 * разрешено ли такое расширение
				 */
				if ($.inArray(sExtension, aAllowedExtensions) === -1) {
					ls.msg.error('', ls.lang.get('plugin.simplecatalog.Errors.image_upload.file_extension_not_allowed', {extension: sExtension, file: sValue}));
					bSuccess = false;
					return false;
				}
				/**
				 * разрешен ли размер файла
				 */
				var iSize = o.size;
				if (iSize && iSize > iMaxImageFileSizeAllowed) {
					ls.msg.error('', ls.lang.get('plugin.simplecatalog.Errors.image_upload.file_size_too_large', {
						current_size: parseInt(iSize/1000),
						max_size: parseInt(iMaxImageFileSizeAllowed/1000),
						file: sValue
					}));
					bSuccess = false;
					return false;
				}
			});
			/**
			 * если произошла ошибка (не допустимое расширение или размер)
			 */
			if (!bSuccess) {
				this.value = '';
				return false;
			}

			$ (ls.simplecatalog_images.selectors.images_upload_form).submit();
		});
	};


	/**
	 * Получить отрендеренный список изображений привязки при загрузке страницы
	 *
	 * @constructor
	 */
	this.LoadTargetsImages = function () {
		var oForm = $ (this.selectors.images_upload_form);
		/**
		 * если не добавлена форма загрузки изображений - нечего подгружать
		 */
		if (oForm.length != 1) return ;
		$ (ls.simplecatalog_images.selectors.images_upload_form).toggleClass('loading');
		ls.ajax(
			aRouter['sc_images'] + 'ajax-get-uploaded-images',
			{
				target_id: oForm.find('input[name="target_id"]').val(),
				target_type: oForm.find('input[name="target_type"]').val()
			},
			function(data) {
				if (data.bStateError) {
					ls.msg.error(data.sMsgTitle, data.sMsg);
				} else {
					/**
					 * добавить хтмл код загруженных изображений
					 */
					$ (ls.simplecatalog_images.selectors.uploaded_images_list).html(data.sText);
				}
				$ (ls.simplecatalog_images.selectors.images_upload_form).toggleClass('loading');
			}
		);
	};


	/**
	 * Установить слушатель удаления изображения
	 *
	 * @constructor
	 */
	this.AssignListenerForDeleteImageLink = function () {
		$ (document).on('click.simplecatalog', this.selectors.delete_uploaded_image, function () {
			/**
			 * подтвердить удаление
			 */
			//if (!confirm('Ok?')) return false;
			var oThis = this;
			ls.ajax(
				aRouter['sc_images'] + 'ajax-delete-uploaded-image',
				{
					target_id: $ (oThis).attr('data-target-id'),
					target_type: $ (oThis).attr('data-target-type'),
					image_id: $ (oThis).attr('data-image-id')
				},
				function(data) {
					if (data.bStateError) {
						ls.msg.error(data.sMsgTitle, data.sMsg);
					} else {
						ls.msg.notice(data.sMsgTitle, data.sMsg);
						/**
						 * удалить отображаемый блок изображения
						 */
						$ (oThis).closest('div').fadeOut(150, function () {
							$ (this).remove();
						});
					}
				}
			);
			return false;
		});
	};


	/**
	 * Установить слушатель сортировки изображений
	 *
	 * @constructor
	 */
	this.AssignListenerForSortingImages = function () {
		// docs: http://api.jqueryui.com/sortable/
		$ (ls.simplecatalog_images.selectors.uploaded_images_list).sortable({
			cursor: 'move',
			update: function(event, ui) {
				/**
				 * получить ид в порядке расположения
				 */
				var aIds = ls.simplecatalog_images.GetImagesSortingOrder();
				var oForm = $ (ls.simplecatalog_images.selectors.images_upload_form);
				/**
				 * сохранить новую сортировку
				 */
				ls.ajax(
					aRouter['sc_images'] + 'ajax-change-images-order',
					{
						target_id: oForm.find('input[name="target_id"]').val(),
						target_type: oForm.find('input[name="target_type"]').val(),
						images_ids: aIds
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


	/**
	 * Получить ид изображений в порядке расположения их на форме
	 *
	 * @constructor
	 */
	this.GetImagesSortingOrder = function () {
		var aImagesIds = [];
		$ (ls.simplecatalog_images.selectors.uploaded_images_list + ' > *').each(function (i, o) {
			var iId = $ (o).find('[data-image-id]').attr('data-image-id');
			aImagesIds.push(iId);
		});
		return aImagesIds;
	};
	
	// ---

	return this;
	
}).call (ls.simplecatalog_images || {}, jQuery);

// ---

jQuery (document).ready (function ($) {

	/**
	 * установить слушатель события загрузки изображений
	 */
	ls.simplecatalog_images.AssignListenerForImagesUpload();

	/**
	 * загрузить прикрепленные изображения
	 */
	ls.simplecatalog_images.LoadTargetsImages();

	/**
	 * слушатель удаления изображения
	 */
	ls.simplecatalog_images.AssignListenerForDeleteImageLink();

	/**
	 * слушатель сортировки изображений
	 */
	ls.simplecatalog_images.AssignListenerForSortingImages();

});
