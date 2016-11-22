
	{if $sMenuItemSelect=='usergroups'}
		<!-- Simplecatalog plugin -->
		<ul class="nav nav-pills">
			<li {if $sMenuSubItemSelect=='index'}class="active"{/if}>
				<a href="{router page='usergroups'}">{$aLang.plugin.simplecatalog.Menu.Usergroups.index}</a>
			</li>
			<li {if $sMenuSubItemSelect=='add'}class="active"{/if}>
				<a href="{router page='usergroups'}add">{$aLang.plugin.simplecatalog.Menu.Usergroups.add}</a>
			</li>
		</ul>
		<!-- /Simplecatalog plugin -->
	{/if}
