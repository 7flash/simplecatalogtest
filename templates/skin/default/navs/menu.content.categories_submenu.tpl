
	{if $sMenuItemSelect=='sccategories'}
		<!-- Simplecatalog plugin -->
		<ul class="nav nav-pills">
			<li {if $sMenuSubItemSelect=='index'}class="active"{/if}>
				<a href="{router page='sccategories'}index/{$sMenuSchemeSelect}">{$aLang.plugin.simplecatalog.Menu.Categories.index}</a>
			</li>
			<li {if $sMenuSubItemSelect=='add'}class="active"{/if}>
				<a href="{router page='sccategories'}add/{$sMenuSchemeSelect}">{$aLang.plugin.simplecatalog.Menu.Categories.add}</a>
			</li>
		</ul>
		<!-- /Simplecatalog plugin -->
	{/if}
