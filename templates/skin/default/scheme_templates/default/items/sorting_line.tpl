
{*
	Селект сортировки продуктов в каталоге
*}

{if $aSortOrderData and SCRootStorage::IsInit()}
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

		<ul class="nav nav-pills">
			{foreach $aSortOrderTypes as $sKey => $sSorting}
				{if in_array($sKey, $aExcludeSortingKeys)}{continue}{/if}
				{$bCurrentSorting = false}
				{if $aSortOrderData.sOrder == $sSorting}
					{$bCurrentSorting = true}
				{/if}
				<li {if $bCurrentSorting}class="active"{/if}>
						{*
                            текущая ли это сортировка
                        *}


						<a href="#" data-sort-desc="{request_filter name=array('sort', 'dir') value=array($sKey, 'desc')}"  data-sort-asc="{request_filter name=array('sort', 'dir') value=array($sKey, 'asc')}" class="js-sc-select-url">{$aLang.plugin.simplecatalog.Products.Items.sorting.$sKey}</a>
				</li>
				{*
					исключение сортировок
				*}


			{/foreach}

			<li {if $aSortOrderData.sWay == 'desc'}class="active"{/if}>
				<a href="#" class="js-change-sort" data-order="{$aSortOrderData.sWay}" style="padding: 7px 13px;">
					<span><i class="ion-ios-arrow-thin-down"></i><i class="ion-ios-arrow-thin-up"></i></span>
				</a>
			</li>
		</ul>
	</div>
{/if}
