
{*
	Вывод продукта
*}

<div class="one-product-wrapper{if !$bProductList} mb-30{/if}" itemscope itemtype="http://schema.org/DataCatalog">
	{*
		базовые данные микроразметки (http://schema.org/)
	*}
	<link itemprop="url" href="{$oProduct->getItemShowWebPath()}" />
	<meta itemprop="name" content="{$oProduct->getFirstFieldTitle()}" />
	<meta itemprop="alternateName" content="{$oProduct->getSeoTitle()|escape}" />
	<meta itemprop="keywords" content="{$oProduct->getSeoKeywords()|escape:'html'}" />
	<meta itemprop="description" content="{$oProduct->getSeoDescription()|escape:'html'}" />
	{*
		протокол Open Graph (http://ogp.me/)
	*}
	<meta property="og:title" content="{$oProduct->getFirstFieldTitle()}" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="{$oProduct->getItemShowWebPath()}" />


	<div class="header-wrapper">
		{*
			изображения продукта
		*}
		{sc_scheme_template scheme=$oScheme file="item/images.tpl"}
		{*
			заголовок продукта и управление
		*}
		<div class="product-header">
			{sc_scheme_template scheme=$oScheme file="item/header.tpl"}
		</div>
	</div>


	{*
		связи продукта - в вкладках
	*}
	{if !$bProductList}
		{sc_scheme_template scheme=$oScheme file="item/links/in_tab.tpl"}
	{/if}

	<ul class="product-fields js-sc-tab-content" data-product-tab-id="0" data-product-tab-group="links" itemprop="text">
		{*
			получить поля продукта БЕЗ первого поля, которое является заголовком продукта и выводится перед полями
		*}
		{assign var=aProductFields value=$oProduct->getProductFieldsWOFirstField()}
		{*
			вывод всех полей продукта, ручной счетчик вместо итерации цикла нужен т.к. могут быть поля, которые не нужно выводить в общем списке продуктов
		*}
		{$iCurrentFieldOrder = 0}
		{foreach from=$aProductFields item=oProductField}
			{*
				ссылка "читать далее" в списке продуктов если требуется отображение только нужного количества полей продукта
			*}
			{if $bProductList and $iCurrentFieldOrder >= $oScheme->getShortViewFieldsCount()}
				<li>
					<a href="{$oProduct->getItemShowWebPath()}" class="view-full-product-page button">{$aLang.plugin.simplecatalog.Products.Items.read_more}</a>
				</li>
				{break}
			{/if}

			{*
				место показа данного поля (отображать в списке или при просмотре полной страницы продукта)
			*}
			{if $bProductList}
				{assign var="sThisPlaceId" value=$SC_FIELD_SHOW_IN_PRODUCT_LIST}
			{else}
				{assign var="sThisPlaceId" value=$SC_FIELD_SHOW_ON_PRODUCT_PAGE}
			{/if}

			{if !$oProductField->getField()->getFieldNeedToBeShownByPlace($sThisPlaceId)}
				{continue}
			{/if}


			<li{if $iCurrentFieldOrder % 2 !== 0} class="second"{/if} itemprop="dataset">
				{*
					заголовок поля
				*}
				{if $oProductField->getField()->getShowFieldNamesInListEnabled()}
					<div class="field-title">
						{$oProductField->getField()->getTitle()}:
					</div>
				{/if}

				{*
					значение поля
					tip: trim пхп используется т.к. strip смарти работает во время компиляции шаблона и не влияет на значения переменных
				*}
				<div class="field-value">{trim(
					{sc_scheme_template scheme=$oScheme file="item/field_value.tpl"
						oField=$oProductField->getField()
						sValue=$oProductField->getDisplayValue()
						oProductField=$oProductField
						oProduct=$oProduct
						oScheme=$oScheme
					}
				)}</div>
			</li>
			{$iCurrentFieldOrder = $iCurrentFieldOrder + 1}
		{/foreach}
	</ul>


	{*
		связи продукта - ссылками, изображениями и в селектах внизу
	*}
	{if !$bProductList}
		{sc_scheme_template scheme=$oScheme file="item/links/as_links.tpl"}
		{sc_scheme_template scheme=$oScheme file="item/links/as_images.tpl"}
		{sc_scheme_template scheme=$oScheme file="item/links/in_select.tpl"}
	{/if}


	{*
		метки на карте
	*}
	{if !$bProductList}
		{sc_scheme_template scheme=$oScheme file="maps/list.tpl"}
	{/if}


	{*
		футер продукта
	*}
	{sc_scheme_template scheme=$oScheme file="item/footer.tpl"}

	{*
		полоска заполненности полей продукта
	*}
	{if $bProductList and count($aProductFields) > 0}
		{sc_scheme_template scheme=$oScheme file="item/fullness_bar.tpl"}
	{/if}

</div>

{*
	отображать комментарии продукта на странице полного просмотра продукта
*}
{if !$bProductList}
	{sc_scheme_template scheme=$oScheme file="item/comments.tpl"}
{/if}
