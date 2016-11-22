
{*
	если полей у схемы нет или ни для одного поля не выбрано отображение в фильтре - показывать блок только админам с сообщением об этом
*}
{if ($aProductFilterData and count($aProductFilterData)>0) or ($oUserCurrent and $oUserCurrent->isAdministrator())}
	<!-- Simplecatalog plugin -->
	<div class="block Simplecatalog ProductFilter">

		{*<header class="block-header sep">
			<h3 title="{$oScheme->getSchemeName()}">{$aLang.plugin.simplecatalog.Blocks.product_filter.title}</h3>
		</header>*}




		<div class="js-product-filter-wrapper mb-15">
				{sc_scheme_template scheme=$oScheme file="filter/filter.tpl"}
		</div>
	</div>
	<!-- /Simplecatalog plugin -->
{/if}
