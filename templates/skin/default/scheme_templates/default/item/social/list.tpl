
{*
	Социальные кнопки для шаринга продукта
*}

{if !$bProductList and $oProduct->getModerationDone()}
	<div class="mb-20 fl-r">
		<a rel="nofollow" target="_blank" class="social-button tw" title="{$aLang.plugin.simplecatalog.Products.Item.social_buttons.tw}"
		   href="http://twitter.com/intent/tweet?text={$oProduct->getFirstFieldTitle()|escape:'url'}+{$oProduct->getItemShowWebPath()}"></a>
		<a rel="nofollow" target="_blank" class="social-button fb" title="{$aLang.plugin.simplecatalog.Products.Item.social_buttons.fb}"
		   href="https://www.facebook.com/sharer/sharer.php?u={$oProduct->getItemShowWebPath()}"></a>
		<a rel="nofollow" target="_blank" class="social-button vk" title="{$aLang.plugin.simplecatalog.Products.Item.social_buttons.vk}"
		   href="http://vkontakte.ru/share.php?url={$oProduct->getItemShowWebPath()}"></a>
		<a rel="nofollow" target="_blank" class="social-button gp" title="{$aLang.plugin.simplecatalog.Products.Item.social_buttons.gp}"
		   href="https://plus.google.com/share?url={$oProduct->getItemShowWebPath()}"></a>
	</div>
{/if}
