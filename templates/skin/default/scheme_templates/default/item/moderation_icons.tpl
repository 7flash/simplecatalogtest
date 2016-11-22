
{*
	Иконки статуса модерации продукта
*}

{if $oProduct->getModerationNeeded()}
	<span class="on-moderation" title="{$aLang.plugin.simplecatalog.Products.Items.on_moderation}"></span>
{/if}
{if $oProduct->getModerationDraft()}
	<span class="saved-as-draft" title="{$aLang.plugin.simplecatalog.Products.Items.saved_as_draft}"></span>
{/if}
{if $oProduct->getModerationDeferred()}
	<i class="sc-icon-time" title="{$aLang.plugin.simplecatalog.Products.Items.saved_as_deferred}"></i>
{/if}
