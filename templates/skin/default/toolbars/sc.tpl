
	<!-- Simplecatalog plugin -->
	{*
		проверка на доступ к какому-либо из разделов админки
	*}
	{if $oUserCurrent and $oUserCurrent->getCanAccessToSimplecatalogToolbarButton()}
		<section class="toolbar-sc-admin">
			<a href="{router page='scheme'}" title="{$aLang.plugin.simplecatalog.Title}">
				<i></i>
			</a>
		</section>
	{/if}
	<!-- /Simplecatalog plugin -->
