<div class="col-xs-12 col-sm-6 {if $noSidebar}col-md-3{else}col-md-4{/if}">
    <div class="role-product role-pink">
        {$oProvider=$oProduct->getUser()->getProvider()}
        <p class="provider"><a href="{$oProvider->getUrlPage()}">{$oProvider->getName()}</a> </p>
        <div class="cover-wrap">
            <div class="cover">
                <a href="{$oProduct->getItemShowWebPath()}" class="img-wrapper" title="{$oProduct->getFirstFieldTitle()}">
                    <img class="img-full" src="{$oProduct->getFirstImageOrDefaultPlaceholderPath()}" alt="{$oProduct->getFirstFieldTitle()}" title="{$oProduct->getFirstFieldTitle()}" />
                </a>
            </div>
        </div>
        <div class="content">
            {assign var=aProductCategories value=$oProduct->getCategories()}
            {if $aProductCategories and count($aProductCategories)>0}
                <p class="categories">
                    {foreach from=$aProductCategories item=oCategory}


                        {capture assign=sCategoryTitle}{strip}
                            {$aLang.plugin.simplecatalog.Products.Items.categories.category_info|ls_lang:"name%%`$oCategory->getName()`":"count%%`$oCategory->getItemsCount()`"}
                        {/strip}{/capture}

                        <a href="{$oCategory->getCategoryUrl($oScheme)}" itemprop="genre" title="{$sCategoryTitle|escape}"
                           {if $aLocalParams.link_target_blank}target="_blank"{/if}>{$oCategory->getName()}</a>
                    {/foreach}
                </p>
            {/if}



            <h2 class="title"><a href="{$oProduct->getItemShowWebPath()}" title="{$oProduct->getFirstFieldTitle()}">{$oProduct->getFirstFieldTitle(50)}</a></h2>
            {hook run="star_rating" type="product" id=$oProduct->getId()}

            <div style="padding-right: 100px; position: relative;">
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
                <ul class="inline-icon-list list-unstyled list-inline" style="position: absolute;
    bottom: -8px;
    right: 0px;">
                    <li>
                        <input type="hidden" class="js-product-count-field" value="1" />
                        {*
                            кнопка "купить"
                        *}
                        <a href="#" class="js-product-buy-button" data-product-id="{$oProduct->getId()}" title="{$aLang.plugin.simplecatalog.shop.buy}">
                            <i class="ion-android-cart"></i>
                        </a>
                    </li>
                    <li>

                        <a href="#">
                            <i class="ion-heart"></i>
                        </a>
                    </li>

                </ul>
            </div>



        </div>


        {*
            валюта
        *}
        <footer>
            {if $oProduct->getModerationDone()}
                <div class="js-product-count-field-wrapper">
                        {*
                            количество
                        *}
                        <input type="hidden" class="js-product-count-field" value="1" />
                        {*
                            кнопка "купить"
                        *}
                        <a href="#" class="footer-pay js-product-buy-button" data-product-id="{$oProduct->getId()}" title="{$aLang.plugin.simplecatalog.shop.buy}">
                            Купить
                        </a>
                </div>
            {/if}

        </footer>
    </div>

</div>