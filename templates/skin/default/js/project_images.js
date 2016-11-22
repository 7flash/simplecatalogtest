var ls = ls || {};

ls.simplecatalog_images = (function ($) {

    // дизейблить кнопку обзора , когда выбрано
    // сделать проверку на повторное нажатие удаления

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
		uploaded_images_list: '.js_c_image_wrapper',
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

        var obThisForm = $(this.selectors.images_upload_form);

        obThisForm.ajaxForm({
			url: aRouter['sc_images'] + 'ajax-upload-images',
			dataType: 'json',
			beforeSend: function() {
                // Hide button
                obThisForm.find(".js_c_file_btn").hide();
                obThisForm.find(".js_c_file_btn_loader").show();
			},
			success: function(data) {
				if (data.bStateError) {
					ls.msg.error(data.sMsgTitle, data.sMsg);

                    obThisForm.find(".js_c_file_btn").show();
				} else {
					ls.msg.notice(data.sMsgTitle, data.sMsg);

					// добавить хтмл код загруженных изображений
					$ (ls.simplecatalog_images.selectors.uploaded_images_list).html(data.sText);
				}
			},
			complete: function(xhr) {
                obThisForm.find(".js_c_file_btn_loader").hide();
			}
		});

		/**
		 * слушатель изменения файла (после корректной проверки сабмитит форму)
		 */
		$(this.selectors.image_upload_input).change(function() {
			var iAlreadyUploadedImages = $ (ls.simplecatalog_images.selectors.uploaded_images_list).find('img').length;
			var iFilesToUpload = this.files.length;
			var iMaxAllowedCount = $ (this).attr('data-max-images-count');
			/**
			 * проверить количество загружаемых изображений
			 */
			if (iAlreadyUploadedImages + iFilesToUpload > iMaxAllowedCount) {
                if (GF_PAGE == "project_edit") {
                    ls.msg.error('',ls.lang.get('plugin.simplecatalog.Errors.image_upload.gf_max_count_exceed',{
                        max_count: iMaxAllowedCount}
                    ));
                } else {
                    ls.msg.error('',ls.lang.get('plugin.simplecatalog.Errors.image_upload.max_count_exceed', {
                         max_count: iMaxAllowedCount,
                         current_count: iAlreadyUploadedImages,
                         try_count: iFilesToUpload
                     }));
                }
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
	 * Установить слушатель удаления изображения
	 *
	 * @constructor
	 */
	this.AssignListenerForDeleteImageLink = function () {

        var obThisForm = $(this.selectors.images_upload_form);

		$ (document).on('click.simplecatalog', this.selectors.delete_uploaded_image, function () {
			var oThis = this;

            if ($(this).hasClass("clicked")) {
                return;
            }

            $(this).addClass("clicked");

            obThisForm.find(".js_c_delete_loader").show();

			ls.ajax(
				aRouter['sc_images'] + 'ajax-delete-uploaded-image',
				{
					target_id: $ (oThis).attr('data-target-id'),
					target_type: $ (oThis).attr('data-target-type'),
					image_id: $ (oThis).attr('data-image-id')
				},
				function(data) {
                    obThisForm.find(".js_c_delete_loader").hide();

					if (data.bStateError) {
						ls.msg.error(data.sMsgTitle, data.sMsg);
                        $(this).removeClass("clicked");
					} else {
						ls.msg.notice(data.sMsgTitle, data.sMsg);
						/**
						 * удалить отображаемый блок изображения
						 */
						$(oThis).closest('div').fadeOut(150, function () {
							$(this).remove();
						});

                        obThisForm.find(".js_c_file_btn").show();
					}
				}
			);
			return false;
		});
	};

	return this;
	
}).call (ls.simplecatalog_images || {}, jQuery);

$(document).ready(function ($) {
	ls.simplecatalog_images.AssignListenerForImagesUpload();

	ls.simplecatalog_images.AssignListenerForDeleteImageLink();
});
