
{*
	Получение хтмл кода для отображения загруженных изображений
*}

{if $aImages}
	{foreach $aImages as $oImage}
		<div>
			<img src="{$oImage->getFilePath()}" />
			<a href="#" class="js-remove-image"
			   data-target-id="{$oImage->getTargetId()}"
			   data-target-type="{$oImage->getTargetType()}"
			   data-image-id="{$oImage->getId()}"
			><i class="sc-icon-remove"></i></a>
		</div>
	{/foreach}
{/if}
