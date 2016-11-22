
{*
	Добавление изображений к объекту

	Передаваемые параметры:

		iTargetId - ид цели
		iTargetType - ид типа цели
		iMaxImagesCount - максимально количество прикрепляемых изображений к объекту
		sText - текст перед блоком изображений
*}

{* форма добавления изображений к сохраненному продукту *}
<script>
	{* для поля продукта загрузки файла *}
	ls.lang.load({lang_load name="plugin.simplecatalog.Errors.files.file_extension_not_allowed,plugin.simplecatalog.Errors.files.file_size_too_large,plugin.simplecatalog.Products.Add.fields.file.delete_file"});

	{* загрузка изображений *}
	ls.lang.load({lang_load name="plugin.simplecatalog.Errors.image_upload.max_count_exceed,plugin.simplecatalog.Errors.image_upload.gf_max_count_exceed,plugin.simplecatalog.Errors.image_upload.file_extension_not_allowed,plugin.simplecatalog.Errors.image_upload.file_size_too_large"});

	$(document).ready(function() {
		$(".js_c_image_fancybox").fancybox();
	});
</script>

<form class="js_c_form_upload form-horizontal" action="" method="post" enctype="multipart/form-data" id="js-sc-images-upload-form">

	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	<input type="hidden" name="target_id" value="{$iTargetId}" />
	<input type="hidden" name="target_type" value="{$iTargetType}" />

	<div class="form-group">
		<label class="control-label col-md-4">
			Картинка <span class="color_red fwb">*</span>
		</label>

		<div class="control-field col-md-7">

			<div style="width: 140px; float: left;">
				<div class="js_c_file_btn btn btn-primary btn-file" {if $arProjectImages}style="display: none;"{/if}>
					<i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;&nbsp;Выбрать фото

					<input type="file" name="images[]" class="sc-images-upload-input js-sc-images-upload-input file" multiple="multiple"
					       data-max-images-count="{$iMaxImagesCount}"
					       data-allowed-extensions="{Config::Get('plugin.simplecatalog.images.allowed_extensions')}"
					       data-max-image-file-size="{Config::Get('plugin.simplecatalog.images.upload_max_file_size')}" />
				</div>

				<div class="js_c_file_btn_loader" style="display: none;">
					<img src="{cfg name="path.static.skin"}/images/project/loader.gif" width="18" height="18" />
					<span>Идет загрузка</span>
				</div>

				<div class="js_c_delete_loader" style="display: none;">
					<img src="{cfg name="path.static.skin"}/images/project/loader.gif" width="18" height="18" />
					<span>Удаление</span>
				</div>
			</div>

			<div class="js_c_image_wrapper" style="width: 200px; float: right;">
				{if $arProjectImages}
					<div>
						<a class="js_c_image_fancybox" rel="group" href="{$arProjectImages.image_detail.url}">
							<img src="{$arProjectImages.image_preview.url}" />
						</a>
						<a class="js-remove-image" data-image-id="{$arProjectImages.image_preview.id}" data-target-type="1" data-target-id="{$iTargetId}"  href="#" title="Удалить"><span class="glyphicon glyphicon-remove-circle"></span></a>
					</div>
				{/if}
			</div>
		</div>
	</div><!--  class="form-group" -->
</form>
