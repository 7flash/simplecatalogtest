
{*
	Обертка для вывода схем и их последних продуктов перед топиками
*}

<div class="Simplecatalog last-products">
	<div class="mb-40">
		{include file="{$aTemplatePathPlugin.simplecatalog}helpers/products/last_products.inject.tpl"
			aLocalParams = [
				bLastProductsHook => true
			]
			aSchemesWithLastProductsData=$aSC_HookLastProducts
		}
	</div>
</div>
