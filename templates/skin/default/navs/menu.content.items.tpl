
	{if $oUserCurrent or ($oScheme->getMapItemsEnabled() and !$oScheme->getNeedToShowMapOnProductItemsPage())}
		<!-- Simplecatalog plugin -->
		<ul class="nav nav-pills">
			{*
				все продукты
			*}
			<li {if $sMenuItemSelect=='index'}class="active"{/if}>
				<a href="{$oScheme->getCatalogItemsWebPath()}">{$aLang.plugin.simplecatalog.Menu.Items.root}</a>
			</li>

			{if $oUserCurrent}
				{*
					продукты на модерации
				*}
				{if $oUserCurrent->getUserCanModerateProductsBySchemeOrIsAdmin($oScheme)}
					<li {if $sMenuItemSelect=='moderation'}class="active"{/if}>
						{hook run='sc_product_items_menu_content_items_moderation_needed_products_count' oScheme=$oScheme iModerationNeededProducts=$iModerationNeededProducts assign=iModerationNeededProducts}
						<a href="{$oScheme->getCatalogModerationNeededItemsWebPath()}">{$aLang.plugin.simplecatalog.Menu.Items.moderation_needed} ({$iModerationNeededProducts})</a>
					</li>
				{/if}
				{*
					может ли пользователь создавать продукты в схеме
				*}
				{if $oUserCurrent->getCanAddNewProductsInScheme($oScheme)}
					{*
						все продукты текущего пользователя (на модерации, промодерированные и отложенные)
					*}
					<li {if $sMenuItemSelect=='my'}class="active"{/if}>
						{hook run='sc_product_items_menu_content_items_my_products_count' oScheme=$oScheme iMyProducts=$iMyProducts assign=iMyProducts}
						<a href="{$oScheme->getCatalogMyItemsWebPath()}">{$aLang.plugin.simplecatalog.Menu.Items.my} ({$iMyProducts})</a>
					</li>
					{*
						черновики текущего пользователя
					*}
					{if $oScheme->getAllowDraftsEnabled()}
						<li {if $sMenuItemSelect=='drafts'}class="active"{/if}>
							{hook run='sc_product_items_menu_content_items_drafts_count' oScheme=$oScheme iDraftsProducts=$iDraftsProducts assign=iDraftsProducts}
							<a href="{$oScheme->getCatalogDraftsItemsWebPath()}">{$aLang.plugin.simplecatalog.Menu.Items.drafts} ({$iDraftsProducts})</a>
						</li>
					{/if}
				{/if}
			{/if}

			{*
				включена ли карта и не является ли она главной страницей каталога (не нужно делать доп. пункт меню)
			*}
			{if $oScheme->getMapItemsEnabled() and !$oScheme->getNeedToShowMapOnProductItemsPage()}
				{*
					все точки на карте
				*}
				<li {if $sMenuItemSelect=='mapitems'}class="active"{/if}>
					{hook run='sc_product_items_menu_content_items_map_items_count' oScheme=$oScheme iTotalMapItemsCount=$iTotalMapItemsCount assign=iTotalMapItemsCount}
					<a href="{$oScheme->getCatalogMapItemsWebPath()}">{$aLang.plugin.simplecatalog.Menu.Items.mapitems} ({$iTotalMapItemsCount})</a>
				</li>
			{/if}
		</ul>
		<!-- /Simplecatalog plugin -->
	{/if}
