
	{if $sMenuItemSelect=='scheme'}
		<!-- Simplecatalog plugin -->
		<ul class="nav nav-pills">
			<li {if $sMenuSubItemSelect=='index'}class="active"{/if}>
				<a href="{router page='scheme'}">{$aLang.plugin.simplecatalog.Menu.Schemes.index}</a>
			</li>
			<li {if $sMenuSubItemSelect=='add'}class="active"{/if}>
				<a href="{router page='scheme'}add">{$aLang.plugin.simplecatalog.Menu.Schemes.add}</a>
			</li>
		</ul>
		<!-- /Simplecatalog plugin -->
	{/if}
