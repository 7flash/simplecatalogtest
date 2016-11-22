
<!-- Simplecatalog plugin -->
{if SCRootStorage::IsInit()}
	{add_block group='toolbar' name='toolbars/sc.tpl' priority=100 plugin='simplecatalog'}
	{add_block group='toolbar' name='toolbars/cart.tpl' priority=50 plugin='simplecatalog'}
	{*
		модальное окно корзины
	*}
	{include file="{$aTemplatePathPlugin.simplecatalog}helpers/cart/modal_cart_items.tpl"}
	{*
		модальное окно получения кода для вставки
	*}
	{include file="{$aTemplatePathPlugin.simplecatalog}helpers/embed/modal_embed_code.tpl"}
{/if}
<!-- /Simplecatalog plugin -->
