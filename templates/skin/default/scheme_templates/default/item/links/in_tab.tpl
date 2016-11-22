
{*
	Связи продукта - отображение вкладок
*}

{assign var=aProductLinksData value=$oProduct->getProductLinksDataWithShowTypeInTab()}

{if $aProductLinksData and count($aProductLinksData) > 0}
	<div class="product-links-in-header">
		{*
			вкладки
		*}
		<div class="links-tabs mb-20">
			{*
				по массиву связей и их настроек
			*}
			{foreach from=$aProductLinksData item=aLinkData}
				{assign var=aProductLinks value=$aLinkData['aProductLinks']}
				{assign var=oLinkSettings value=$aLinkData['oLinkSettings']}

				<a href="#" class="js-sc-tab" data-product-tab-id="{$oLinkSettings->getId()}" data-product-tab-group="links">
					{$oLinkSettings->getDisplayName()}
					{*
						не выводить количество для типа связей "1 к 1"
					*}
					{if !$oLinkSettings->getTypeHasOne()}
						({count($aProductLinks)})
					{/if}
				</a>
			{/foreach}

			<a href="#" class="active js-sc-tab" data-product-tab-id="0" data-product-tab-group="links">{$aLang.plugin.simplecatalog.Products.Item.links.main_tab}</a>
		</div>

		{*
			вывод списка продуктов связей
		*}
		<div class="product-links-list">
			{*
				для каждой вкладки
			*}
			{foreach from=$aProductLinksData item=aLinkData}
				{assign var=aProductLinks value=$aLinkData['aProductLinks']}
				{assign var=oLinkSettings value=$aLinkData['oLinkSettings']}

				<div class="linked-products mb-20 js-sc-tab-content" data-product-tab-id="{$oLinkSettings->getId()}" data-product-tab-group="links" style="display: none;">
					{*
						описание
					*}
					{if $oLinkSettings->getDescription()}
						<div class="description">
							{$oLinkSettings->getDescription()|nl2br}
						</div>
					{/if}
					{*
						вывод продуктов
					*}
					<div>
						{foreach from=$aProductLinks item=oLink}
							{sc_scheme_template scheme=$oScheme file="item/links/elements/image.tpl"}
						{/foreach}
					</div>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
