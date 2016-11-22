
{*
	Форма поиска по каталогу
*}

<div class="mb-20">
	<form action="{$oScheme->getCatalogItemsSearchWebPath()}" method="get" enctype="application/x-www-form-urlencoded" class="products-search-form-abc">
		<div class="input-wrapper">
			<input type="text" name="filter[q]" value="{$_aRequest.filter.q}" class="input-text input-width-full js-sc-product-title-search" maxlength="2000"
				   placeholder="{$aLang.plugin.simplecatalog.Products.Items.search.ph}" />
			<input type="submit" value="&rarr;" class="input-submit" />
		</div>
		{*
			добавить автокомплитер для поля поиска по полям продуктов схемы с поддержкой изображений в ответе
		*}
		<script>
			jQuery (document).ready (function ($) {
				ls.simplecatalog_autocompleter.AddAutocompleterWithImagesSupportInItems(
					$ ('div.Simplecatalog .js-sc-product-title-search'),
					aRouter['product-search'] + 'ajax-title-search/{$oScheme->getSchemeUrl()}',
					{Config::Get('plugin.simplecatalog.search.products.query_length.min')}
				);
			});
		</script>

		{hook run='sc_product_items_search_form_after' aProducts=$aProducts oScheme=$oScheme}
	</form>
</div>
