
	{if $sMenuItemSelect=='sc_links'}
		<!-- Simplecatalog plugin -->
		<ul class="nav nav-pills">
			<li {if $sMenuSubItemSelect=='index'}class="active"{/if}>
				<a href="{router page='sc_links'}index/{$sMenuSchemeSelect}">{$aLang.plugin.simplecatalog.Menu.Links.index}</a>
			</li>
			<li {if $sMenuSubItemSelect=='add'}class="active"{/if}>
				<a href="{router page='sc_links'}add/{$sMenuSchemeSelect}">{$aLang.plugin.simplecatalog.Menu.Links.add}</a>
			</li>
		</ul>
		<!-- /Simplecatalog plugin -->
	{/if}
