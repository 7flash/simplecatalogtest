
{if $aSC_BlockLastProducts and count($aSC_BlockLastProducts) > 0}
	<!-- Simplecatalog plugin -->
	<div class="block Simplecatalog last-products">
		<header class="block-header sep">
			<h3>{$aLang.plugin.simplecatalog.Blocks.last_products_in_sidebar.title}</h3>
		</header>
		<div class="block-content">
			{include file="{$aTemplatePathPlugin.simplecatalog}helpers/products/last_products.inject.tpl" aSchemesWithLastProductsData=$aSC_BlockLastProducts}
		</div>
	</div>
	<!-- /Simplecatalog plugin -->
{/if}
