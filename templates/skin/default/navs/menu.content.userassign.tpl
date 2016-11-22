
	{if $sMenuItemSelect=='userassign'}
		<!-- Simplecatalog plugin -->
		<ul class="nav nav-pills">
			<li {if $sMenuSubItemSelect=='index'}class="active"{/if}>
				<a href="{router page='userassign'}">{$aLang.plugin.simplecatalog.Menu.Userassign.index}</a>
			</li>
			<li {if $sMenuSubItemSelect=='add'}class="active"{/if}>
				<a href="{router page='userassign'}add">{$aLang.plugin.simplecatalog.Menu.Userassign.add}</a>
			</li>
		</ul>
		<!-- /Simplecatalog plugin -->
	{/if}
