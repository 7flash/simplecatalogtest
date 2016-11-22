{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.categories.tpl"}

{if $sMenuSchemeSelect}
	{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.categories_submenu.tpl"}

	<div class="Simplecatalog Categories Index">
		<h2 class="page-header">{$aLang.plugin.simplecatalog.Categories.List.title} (<span>{count($aCategoryTree)}</span>)</h2>

		{if $aCategoryTree and count($aCategoryTree)>0}

			<table class="table-items-list">
				<thead>
					<tr>
						<th>{$aLang.plugin.simplecatalog.Categories.List.table_header.name}</th>
						<th>{$aLang.plugin.simplecatalog.Categories.List.table_header.url}</th>
						<th>{$aLang.plugin.simplecatalog.Categories.List.table_header.full_url}</th>
						<th>{$aLang.plugin.simplecatalog.Categories.List.table_header.children_count}</th>
						<th>{$aLang.plugin.simplecatalog.Categories.List.table_header.items_count}</th>
						<th>{$aLang.plugin.simplecatalog.Categories.List.table_header.sorting}</th>

						<th>{$aLang.plugin.simplecatalog.Controls}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$aCategoryTree item=aCategoryItem}
						{assign var=oCategory value=$aCategoryItem['entity']}
						{assign var=iLevel value=$aCategoryItem['level']}
						{assign var=iChildrenCount value=$aCategoryItem['children_count']}

						<tr{if $aCategoryItem@iteration % 2 == 0} class="second"{/if}>
							<td class="nameleft" style="padding-left: {$iLevel*20}px">{$oCategory->getName()}</td>
							<td class="nameleft">{$oCategory->getUrl()}</td>
							<td class="nameleft">{$oCategory->getFullUrl()}</td>
							<td>{$iChildrenCount}</td>
							<td>{$oCategory->getItemsCount()}</td>
							<td>{$oCategory->getSorting()}</td>
							<td>
								<a href="{$oCategory->getEditWebPath($sMenuSchemeSelect)}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>
								<a href="{$oCategory->getDeleteWebPath($sMenuSchemeSelect)}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>

		{else}
			{$aLang.plugin.simplecatalog.Categories.List.no_categories}
		{/if}
	</div>

{elseif $aSchemesMenuItems and count($aSchemesMenuItems)>0}
	{$aLang.plugin.simplecatalog.Categories.select_scheme_you_want_to_work_with}
{/if}

{include file='footer.tpl'}