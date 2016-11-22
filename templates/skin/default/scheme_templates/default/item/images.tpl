
{*
	Изображения продукта
*}

{if $oFirstProductImage = $oProduct->getFirstImage()}
	{$aImagesWOFirst = $oProduct->getImagesWOFirst()}
	{$iAltImagesCount = count($aImagesWOFirst)}
	{$sFirstFieldTitle = $oProduct->getFirstFieldTitle()}

	<div class="product-images-list">
		{*
			первое изображение (главное)
		*}
		<a href="{$oFirstProductImage->getFilePath()}" class="img-wrapper js-alt-images" rel="{if $iAltImagesCount}[product-images-{$oProduct->getId()}]{/if}" title="{$sFirstFieldTitle}">
			<img class="main" src="{$oFirstProductImage->getFilePath()}" alt="{$sFirstFieldTitle}" title="{$sFirstFieldTitle}" />
		</a>
		<meta itemprop="image" content="{$oFirstProductImage->getFilePath()}" />
		<meta property="og:image" content="{$oFirstProductImage->getFilePath()}" />
		{*
			если существуют ещё изображения
		*}
		{if $aImagesWOFirst and $iAltImagesCount}
			<div class="product-images-alternative {if $bProductList}no-previews{/if}">
				{foreach $aImagesWOFirst as $oImageAlt}
					{$sTitle = "{$sFirstFieldTitle} ({($oImageAlt@iteration) + 1})"}
					<div class="img-container">
						<a href="{$oImageAlt->getFilePath()}" class="img-wrapper js-alt-images" rel="[product-images-{$oProduct->getId()}]" title="{$sTitle}">
							{*
								показывать превью остальных изображений только на полной странице продукта
							*}
							{if !$bProductList}
								<img src="{$oImageAlt->getFilePath()}" alt="{$sTitle}" title="{$sTitle}" />
							{/if}
						</a>
						<meta itemprop="image" content="{$oImageAlt->getFilePath()}" />
						<meta property="og:image" content="{$oImageAlt->getFilePath()}" />
					</div>
				{/foreach}
				{*
					в списке продуктов показывать подсказку сколько ещё изображений у продукта
				*}
				{if $bProductList}
					{capture assign=sImagesCount}{$aLang.plugin.simplecatalog.Products.Items.and_other_photos|ls_lang:"count%%`$iAltImagesCount`"}{/capture}
					<div class="more-photos-tip">{$iAltImagesCount|declension:$sImagesCount:{Config::Get('lang.current')}}</div>
				{/if}
			</div>
		{/if}
		{*
			если это полная страница продукта - подключить просмотр в модальном окне
		*}
		{if !$bProductList}
			{include file="{$aTemplatePathPlugin.simplecatalog}helpers/modals/images_modal.tpl"}
		{/if}
	</div>
{/if}
