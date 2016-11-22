
	{if $sMenuItemSelect=='scheme_fields'}
		<!-- Simplecatalog plugin -->
		<ul class="nav nav-pills">
			<li {if $sMenuSubItemSelect=='schemefields'}class="active"{/if}>
				<a href="{$oScheme->getFieldsListWebPath()}">{$aLang.plugin.simplecatalog.Menu.Fields.index}</a>
			</li>
			<li {if $sMenuSubItemSelect=='add'}class="active"{/if}>
				<a href="{$oScheme->getNewFieldAddWebPath()}">{$aLang.plugin.simplecatalog.Menu.Fields.add}</a>
			</li>
		</ul>
		<!-- /Simplecatalog plugin -->
	{/if}
