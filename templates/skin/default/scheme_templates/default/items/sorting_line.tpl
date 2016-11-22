
{*
	Селект сортировки продуктов в каталоге
*}

{if $aSortOrderData}
	<div class="logic-item-on-left">
		{$aSortOrderTypes = Config::Get('plugin.simplecatalog.product.allowed_sort_order_types')}
		{*
			если магазин не включен для схемы - убрать сортировку по ценам
		*}
		{if !$oScheme->getShopEnabled()}
			{assign var=aExcludeSortingKeys value=array('price')}
		{else}
			{assign var=aExcludeSortingKeys value=array()}
		{/if}

		{$aLang.plugin.simplecatalog.Products.Items.sorting.title}

		<select class="input-text input-width-150 js-sc-select-url">
			{foreach $aSortOrderTypes as $sKey => $sSorting}
				{*
					исключение сортировок
				*}
				{if in_array($sKey, $aExcludeSortingKeys)}{continue}{/if}

				{foreach ['desc', 'asc'] as $sDir}
					{*
						текущая ли это сортировка
					*}
					{$bCurrentSorting = false}
					{if $aSortOrderData.sOrder == $sSorting and $sDir == $aSortOrderData.sWay}
						{$bCurrentSorting = true}
					{/if}

					<option value="{request_filter
						name=array('sort', 'dir')
						value=array($sKey, $sDir)
					}" {if $bCurrentSorting}selected="selected"{/if}>{$aLang.plugin.simplecatalog.Products.Items.sorting.$sKey} {if $sDir=='desc'}&#9660;{else}&#9650;{/if}</option>
				{/foreach}
			{/foreach}
		</select>
	</div>
{/if}
