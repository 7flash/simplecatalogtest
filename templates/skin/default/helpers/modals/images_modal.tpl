
{*
	Просмотр изображений в модальном окне
*}

<script src="{Config::Get('path.root.engine_lib')}/external/prettyPhoto/js/prettyPhoto.js"></script>
<link rel='stylesheet' type='text/css' href="{Config::Get('path.root.engine_lib')}/external/prettyPhoto/css/prettyPhoto.css" />

<script>
	jQuery(document).ready(function ($) {
		$ ('div.Simplecatalog .js-alt-images').prettyPhoto({
			{if $bDisablePreviews}
				overlay_gallery: false,
			{/if}
			social_tools: '',
			show_title: false,
			slideshow: false,
			deeplinking: false
		});
	});
</script>
