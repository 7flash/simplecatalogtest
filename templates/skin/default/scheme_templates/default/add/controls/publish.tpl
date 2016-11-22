
{*
	Кнопки публикации продукта
*}

<input type="submit" value="{$aLang.plugin.simplecatalog.Products.Add.submit_title}" name="submit_add" class="button button-primary" />
{*
	если в схеме разрешены черновики
*}
{if $oScheme->getAllowDraftsEnabled()}
	<input type="submit" value="{$aLang.plugin.simplecatalog.Products.Add.save_draft}" name="submit_save_draft" class="button" />
{/if}
