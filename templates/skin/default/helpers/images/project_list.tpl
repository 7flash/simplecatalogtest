{if $aImages}
	<div>
		<a class="js_c_image_fancybox" rel="group" href="{$aImages.image_detail->getFilePath()}">
			<img src="{$aImages.image_preview->getFilePath()}" />
		</a>
		<a href="#" class="js-remove-image"
		   data-target-id="{$aImages.image_preview->getTargetId()}"
		   data-target-type="{$aImages.image_preview->getTargetType()}"
		   data-image-id="{$aImages.image_preview->getId()}"
		   title="Удалить"><span class="glyphicon glyphicon-remove-circle"></span></a>
	</div>
{/if}
