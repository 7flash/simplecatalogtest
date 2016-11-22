{assign var="sidebarPosition" value='left'}
{include file='header.tpl' menu='people'}


{include file='actions/ActionProfile/profile_top.tpl'}
{include file='menu.profile_created.tpl'}

	<div class="Simplecatalog Product Items Profile CreatedProducts">
		<h2 class="page-header">{$oScheme->getSchemeName()}{if $iTotalProductCount} (<span>{$iTotalProductCount}</span>){/if}</h2>

		<div class="mb-20">{$oScheme->getDescription()|nl2br}</div>

		{sc_scheme_template scheme=$oScheme file="items.tpl"}
	</div>

{include file='footer.tpl'}