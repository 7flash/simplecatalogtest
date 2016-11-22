
{*
	Добавление изображений к объекту

	Передаваемые параметры:

		iTargetId - ид цели
		iTargetType - ид типа цели
		iMaxImagesCount - максимально количество прикрепляемых изображений к объекту
		sText - текст перед блоком изображений
*}

<script>
	{*
		загрузка изображений
	*}
	ls.lang.load({lang_load name="plugin.simplecatalog.Errors.image_upload.max_count_exceed,plugin.simplecatalog.Errors.image_upload.file_extension_not_allowed,plugin.simplecatalog.Errors.image_upload.file_size_too_large"});
</script>

<form action="" method="post" enctype="multipart/form-data" id="js-sc-images-upload-form">
	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	<input type="hidden" name="target_id" value="{$iTargetId}" />
	<input type="hidden" name="target_type" value="{$iTargetType}" />

	{$sText} &nbsp;

	<input type="file" name="images[]" class="sc-images-upload-input js-sc-images-upload-input file" multiple="multiple"
		   data-max-images-count="{$iMaxImagesCount}"
		   data-allowed-extensions="{Config::Get('plugin.simplecatalog.images.allowed_extensions')}"
		   data-max-image-file-size="{Config::Get('plugin.simplecatalog.images.upload_max_file_size')}"
	/>

	<div id="js-sc-images-uploaded-list" class="sc-images-uploaded-list"></div>
</form>
