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

<div class="container">
	<ul class="nav-line black list-unstyled clearfix mb-30">
		<li>{$oScheme->getName()}</li>
		{assign var=aProductCategories value=$oProduct->getCategories()}
		{if $aProductCategories and count($aProductCategories)>0}
				{foreach from=$aProductCategories item=oCategory}

					{*{capture assign=sCategoryTitle}{strip}
						{$aLang.plugin.simplecatalog.Products.Items.categories.category_info|ls_lang:"name%%`$oCategory->getName()`":"count%%`$oCategory->getItemsCount()`"}
					{/strip}{/capture}

					<a href="{$oCategory->getCategoryUrl($oScheme)}" itemprop="genre" title="{$sCategoryTitle|escape}"
					   {if $aLocalParams.link_target_blank}target="_blank"{/if}>{$oCategory->getName()}</a>*}

					<li>{$oCategory->getName()}</li>
				{/foreach}
		{/if}

	</ul>
</div>

<div class="role-product-full role-pink">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-3">
				<ul class="role-param-list list-unstyled">
					{assign var=aProductFields value=$oProduct->getProductFieldsWOFirstField()}
					{*
                        вывод всех полей продукта, ручной счетчик вместо итерации цикла нужен т.к. могут быть поля, которые не нужно выводить в общем списке продуктов
                    *}
					{$iCurrentFieldOrder = 0}
					{foreach from=$aProductFields item=oProductField}

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
			</div>
			<div class="col-xs-12 col-md-6">
				{sc_scheme_template scheme=$oScheme file="item/images.tpl"}
			</div>
			<div class="col-xs-12 col-md-3">
				<h2 class="title">{$oProduct->getFirstFieldTitle()}</h2>
				{hook run="star_rating" type="product" id=$oProduct->getId()}



				{if $oUserCurrent and $oUserCurrent->getCanManageProduct($oProduct)}
					<ul class="controls list-unstyled list-inline">
						<li>
							<a href="{$oProduct->getItemEditWebPath()}" class="edit"><i class="sc-icon-edit"></i>{$aLang.plugin.simplecatalog.Edit}</a>
						</li>
						<li>
							<a href="{$oProduct->getItemDeleteWebPath()}" class="delete js-question"><i class="sc-icon-remove"></i>{$aLang.plugin.simplecatalog.Delete}</a>
						</li>
						{hook run='sc_product_item_header_edit_controls_item' oProduct=$oProduct oScheme=$oScheme bProductList=$bProductList}
					</ul>
				{/if}
				<br>

				<div class="role-price">
					{if $oProduct->getPriceNewCalculated()}
						<p class="old"><s>{$oProduct->getPrice()}</s>                    {$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}
						</p>
						<p class="price">{$oProduct->getPriceNewCalculated()}                    {$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}
						</p>
					{else}
						<p class="old"></p>
						<p class="price">{$oProduct->getPrice()}                    {$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}
						</p>

					{/if}
				</div>

				<br>

				<ul class="role-param-list list-unstyled">
					<li style="text-align: center">
						<div class="js-product-count-field-wrapper">
							{*
                                количество
                            *}
							<input type="hidden" class="js-product-count-field" value="1" />
							{*
                                кнопка "купить"
                            *}
							<a href="#" class="footer-pay js-product-buy-button" data-product-id="{$oProduct->getId()}" title="{$aLang.plugin.simplecatalog.shop.buy}">
								<i class="ion-android-cart"></i>
							</a>
						</div>
					</li>
					<li style="text-align: center">
						<a href="#"><i class="ion-heart"></i></a>
					</li>
					<li style="text-align: center">
						<a href="#">Доставка</a>
					</li>
				</ul>
				<br>
				<div class="js-product-count-field-wrapper">
					{*
                        количество
                    *}
					<input type="hidden" class="js-product-count-field" value="1" />
					{*
                        кнопка "купить"
                    *}
					<a href="#" class="btn-pay js-product-buy-button" data-product-id="{$oProduct->getId()}" title="{$aLang.plugin.simplecatalog.shop.buy}">
						Купить
					</a>
				</div>


			</div>
	</div>

</div>
</div>

<div style="display: none;">
	<div class="block-random-product" ></div>
	<div style="margin: 15px 0 20px; text-align: center"><i class="btn-refrash ion-loop"></i></div>

</div>
<style>
	.btn-refrash {
		width: 85px;
		height: 85px;
		line-height: 85px;
		display: inline-block;
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
		border-radius:  50%;
		background-color: #fff;
		font-size: 50px;
		color: #302e37;
		cursor: pointer;
	}
</style>

<script>
	$(function(){
		ls.simplecatalog_product.getRandomProduct({$oCategory->getId()}, {$oProduct->getId()});
		$('.btn-refrash').click(function() {
			ls.simplecatalog_product.getRandomProduct({$oCategory->getId()}, {$oProduct->getId()})
		})
	})
</script>


{*
	отображать комментарии продукта на странице полного просмотра продукта

{if !$bProductList}
	{sc_scheme_template scheme=$oScheme file="item/comments.tpl"}
{/if}*}
