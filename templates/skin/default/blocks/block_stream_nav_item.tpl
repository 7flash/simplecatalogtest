
<!-- Simplecatalog plugin -->
{foreach $aSchemesWithAllowedComments as $oSchemeWithAllowedComments}
	{assign var="sPostfix" value=$oSchemeWithAllowedComments->getSchemeUrl()}
	<li class="js-block-stream-item" data-type="products_{$sPostfix}"><a href="#">{$oSchemeWithAllowedComments->getSchemeName()}</a></li>
	<script>
		jQuery (document).ready (function ($) {
			ls.blocks.options.type.stream_products_{$sPostfix} = {
				url: aRouter['product-comments'] + 'ajax-online-comments/{$sPostfix}'
			};
		});
	</script>
{/foreach}
<!-- /Simplecatalog plugin -->
