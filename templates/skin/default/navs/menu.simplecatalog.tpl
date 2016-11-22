
		<!-- Simplecatalog plugin -->
		<ul class="Simplecatalog nav nav-menu">
			{if $oUserCurrent}
				{*
					схемы
				*}
				{if $oUserCurrent->getCanUserManageSchemesOrIsAdmin()}
					<li {if $sMenuItemSelect=='scheme'}class="active"{/if}>
						<a href="{router page='scheme'}">{$aLang.plugin.simplecatalog.Menu.Schemes.root}</a>
					</li>
				{/if}
				{*
					группы прав
				*}
				{if $oUserCurrent->getCanUserManageUserGroupsOrIsAdmin()}
					<li {if $sMenuItemSelect=='usergroups'}class="active"{/if}>
						<a href="{router page='usergroups'}">{$aLang.plugin.simplecatalog.Menu.Usergroups.root}</a>
					</li>
				{/if}
				{*
					назначение пользователям групп прав
				*}
				{if $oUserCurrent->getCanUserManageUsersAssignToGroupsOrIsAdmin()}
					<li {if $sMenuItemSelect=='userassign'}class="active"{/if}>
						<a href="{router page='userassign'}">{$aLang.plugin.simplecatalog.Menu.Userassign.root}</a>
					</li>
				{/if}
				{*
					категории
				*}
				{if $oUserCurrent->getCanUserManageCategoriesOrIsAdmin()}
					<li {if $sMenuItemSelect=='sccategories'}class="active"{/if}>
						<a href="{router page='sccategories'}">{$aLang.plugin.simplecatalog.Menu.Categories.root}</a>
					</li>
				{/if}
				{*
					системный список продуктов
				*}
				{if $oUserCurrent->isAdministrator()}
					<li {if $sMenuItemSelect=='index'}class="active"{/if}>
						<a href="{router page='product'}index">{$aLang.plugin.simplecatalog.Menu.Products.root}</a>
					</li>
				{/if}
				{*
					заказы
				*}
				{if $oUserCurrent->getCanUserManageOrdersOrIsAdmin()}
					<li {if $sMenuItemSelect=='sc_shop'}class="active"{/if}>
						<a href="{router page='shop'}">{$aLang.plugin.simplecatalog.Menu.Orders.root}</a>
					</li>
				{/if}
				{*
					связи
				*}
				{if $oUserCurrent->getCanUserManageLinksOrIsAdmin()}
					<li {if $sMenuItemSelect=='sc_links'}class="active"{/if}>
						<a href="{router page='sc_links'}">{$aLang.plugin.simplecatalog.Menu.Links.root}</a>
					</li>
				{/if}

				{hook run='sc_menu_admin_item' oUserCurrent=$oUserCurrent}
			{/if}
		</ul>
		<!-- /Simplecatalog plugin -->
