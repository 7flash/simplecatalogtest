
{*
	Вывод схем и их последних продуктов перед топиками или в сайдбаре
*}

{*
	по схемам
*}
{if $aSchemesWithLastProductsData and count($aSchemesWithLastProductsData) > 0}
	{foreach $aSchemesWithLastProductsData as $aData}
		{$oScheme = $aData.oScheme}
		{$aProducts = $aData.aProducts}

		{*
			используется при выводе через хук перед топиками
		*}
		{if $aLocalParams.bLastProductsHook}
			<h2 class="page-header mt30 sc-last-products-hook-scheme-title">
				{$aLang.plugin.simplecatalog.Blocks.last_products_in_sidebar.title_for_hook} "<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>"
			</h2>
		{else}
			<h2 class="sc-last-products-scheme-title">
				<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>
			</h2>
		{/if}

		{*
			по продуктам схем
		*}
		{foreach $aProducts as $oProduct}
			<div class="one-product-short">
				{if $aLocalParams.bLastProductsHook}
					<div class="fl-r">
						{*
							если включен функционал интернет-магазина и продукт платный
						*}
						{if $oScheme->getShopEnabled() and floatval($oProduct->getPrice())}
							{sc_scheme_template scheme=$oScheme file="item/shop/price_and_buy_short.tpl"}
						{/if}
					</div>
				{/if}
				{*
					первое изображение (главное)
				*}
				{if $oFirstProductImage = $oProduct->getFirstImage()}
					<img class="preview" src="{$oFirstProductImage->getFilePath()}" alt="{$oProduct->getFirstFieldTitle()|escape:'html'}" title="{$oProduct->getFirstFieldTitle()|escape:'html'}" />
				{/if}

				<div class="oh">
					<div>
						<a class="title" href="{$oProduct->getItemShowWebPath()}">{$oProduct->getFirstFieldTitle(70)}</a>
					</div>
					<ul class="small-footer-details">
						<li>
							<time datetime="{date_format date=$oProduct->getAddDate() format='c'}" title="{date_format date=$oProduct->getAddDate() format='j F Y, H:i'}">
								{date_format date=$oProduct->getAddDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
							</time>
						</li>
						{*
							количество комментариев
						*}
						{if $aLocalParams.bLastProductsHook and $oProduct->getCommentsEnabled()}
							<li class="secondary-info">
								<a href="{$oProduct->getItemShowWebPath()}#comments" title="{$aLang.plugin.simplecatalog.common.comments_count}"><i class="sc-icon-comment"></i>{$oProduct->getCommentCount()}</a>
							</li>
						{/if}
						{*
							количество меток на карте
						*}
						{if $aLocalParams.bLastProductsHook and $oScheme->getMapItemsEnabled()}
							{$iProductMapItemsCount = count($oProduct->getMapItems())}
							{if $iProductMapItemsCount}
								<li class="secondary-info">
									<a href="{$oProduct->getItemShowWebPath()}#product_map" title="{$aLang.plugin.simplecatalog.common.map_items_count}"><i class="sc-icon-map-marker"></i>{$iProductMapItemsCount}</a>
								</li>
							{/if}
						{/if}
						{*
							количество просмотров продукта
						*}
						{if $aLocalParams.bLastProductsHook}
							{if $iViewsCount = $oProduct->getViewsCount()}
								<li class="secondary-info">
									<span title="{$aLang.plugin.simplecatalog.common.views_count}"><i class="sc-icon-eye-open"></i>{$iViewsCount}</span>
								</li>
							{/if}
						{/if}
					</ul>
					{if !$aLocalParams.bLastProductsHook}
						<div class="mb-5"></div>
						{*
							если включен функционал интернет-магазина и продукт платный
						*}
						{if $oScheme->getShopEnabled() and floatval($oProduct->getPrice())}
							{sc_scheme_template scheme=$oScheme file="item/shop/price_and_buy_short.tpl"}
						{/if}
					{/if}
				</div>
			</div>
		{/foreach}
	{/foreach}

{else}
	{$aLang.plugin.simplecatalog.Blocks.last_products_in_sidebar.no_last_products}
{/if}
