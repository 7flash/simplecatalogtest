
{*
	Форма добавления продукта
*}

<h2 class="page-header mb-30">
	{capture assign=sSchemeLink}<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>{/capture}

	{if $_aRequest.id}
		{$aLang.plugin.simplecatalog.Products.Add.titles.edit|ls_lang:"scheme%%`$sSchemeLink`"}
	{else}
		{$aLang.plugin.simplecatalog.Products.Add.titles.new|ls_lang:"scheme%%`$sSchemeLink`"}
	{/if}
	{*
		иконки статуса
	*}
	{if $_aRequest.moderation_needed}
		<span class="on-moderation" title="{$aLang.plugin.simplecatalog.Products.Items.on_moderation}"></span>
	{/if}
	{if $_aRequest.moderation_saved_as_draft}
		<span class="saved-as-draft" title="{$aLang.plugin.simplecatalog.Products.Items.saved_as_draft}"></span>
	{/if}
	{if $_aRequest.moderation_saved_as_deferred}
		<i class="sc-icon-time" title="{$aLang.plugin.simplecatalog.Products.Items.saved_as_deferred}"></i>
	{/if}
	{*
		ссылка на продукт для удобного доступа
	*}
	{if $_aRequest.item_show_web_path}
		<a href="{$_aRequest.item_show_web_path}" target="_blank" title="{$aLang.plugin.simplecatalog.Products.Add.item_show_web_path}"><i class="sc-icon-share-alt"></i></a>
	{/if}
</h2>

<script>
	{*
		для поля продукта загрузки файла
	*}
	ls.lang.load({lang_load name="plugin.simplecatalog.Errors.files.file_extension_not_allowed,plugin.simplecatalog.Errors.files.file_size_too_large,plugin.simplecatalog.Products.Add.fields.file.delete_file"});
</script>


{*
	форма добавления изображений к сохраненному продукту
*}
{if $_aRequest.id and $oScheme->getMaxImagesCount()}
	<div class="title-underline">
		{include file="{$aTemplatePathPlugin.simplecatalog}helpers/images/add.tpl"
			iTargetId = $_aRequest.id
			iTargetType = PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS
			iMaxImagesCount = $oScheme->getMaxImagesCount()
			sText = $aLang.plugin.simplecatalog.Products.Add.images_of_product
		}
	</div>
{/if}


{*
	редактор
*}
{include file='editor.tpl'}


<form action="{router page='product'}add/{$oScheme->getSchemeUrl()}" method="post" enctype="multipart/form-data">
	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	<input type="hidden" name="id" value="{$_aRequest.id}" />

	{*
		для сохранения статусов и ссылки на продукт если при отправке формы не прошла валидация
	*}
	<input type="hidden" name="moderation_needed" value="{$_aRequest.moderation_needed}" />
	<input type="hidden" name="moderation_saved_as_draft" value="{$_aRequest.moderation_saved_as_draft}" />
	<input type="hidden" name="moderation_saved_as_deferred" value="{$_aRequest.moderation_saved_as_deferred}" />
	<input type="hidden" name="item_show_web_path" value="{$_aRequest.item_show_web_path}" />

	{*
		вывод полей продукта
	*}
	{sc_scheme_template scheme=$oScheme file="add/fields_list.tpl"}


	<div>
		{$aLang.plugin.simplecatalog.Products.Add.mandatory}
	</div>

	{*
		ЧПУ, СЕО, комментарии, категории, флаг загрузки изображений, цены, связи, карты, сообщение о необходимости модерации продукта и отложенная публикация
	*}
	{sc_scheme_template scheme=$oScheme file="add/footer.tpl"}

	{*
		кнопки публикации
	*}
	{sc_scheme_template scheme=$oScheme file="add/controls/publish.tpl"}
</form>
