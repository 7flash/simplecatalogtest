
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

		<meta itemprop="image" content="{$oFirstProductImage->getFilePath()}" />
		<meta property="og:image" content="{$oFirstProductImage->getFilePath()}" />

		<div class="fotorama" data-allowfullscreen="native" data-width="100%" data-cropToFit="true" data-caption="overlay">
			{$aPhotos=$oProduct->getImages()}
				{foreach from=$aPhotos item=oPhoto}
					<img src="{$oPhoto->getFilePath()}" />
				{/foreach}

		</div>

	</div>
{/if}




<style>
	.fotorama__thumbs_previews {
		margin-top: 40px;;
		overflow: hidden;
		 background-color: #fff;
	}

</style>
