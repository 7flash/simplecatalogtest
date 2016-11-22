<div class="container">
    <h2 class="reviews-title">Похожие товары</h2>

    <div class="row">
        {foreach $aProducts as $oProduct}
            {include file="./scheme_templates/mini/items.item.tpl"
            bProductList=true
            noSidebar=true
            oScheme=$oProduct->getScheme()
            }
        {/foreach}
    </div>
</div>