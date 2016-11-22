{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.schemes.tpl"}

	<div class="Simplecatalog Scheme Add">
		<h2 class="page-header title-underline">
			{if $_aRequest.id}
				{$aLang.plugin.simplecatalog.Schemes.Add.titles.edit}
			{else}
				{$aLang.plugin.simplecatalog.Schemes.Add.titles.new}
			{/if}
		</h2>

		{*
			редактор для описания схемы
		*}
		{include file='editor.tpl'}

		<form action="{router page='scheme'}add" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />

			<input type="hidden" name="id" value="{$_aRequest.id}" />

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.scheme_url}</dt>
				<dd>
					<input type="text" name="scheme_url" value="{$_aRequest.scheme_url}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Schemes.Add.scheme_url_ph}" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.scheme_name}</dt>
				<dd>
					<input type="text" name="scheme_name" value="{$_aRequest.scheme_name}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Schemes.Add.scheme_name_ph}" />
				</dd>
			</dl>


			<div class="mb-20">
				<div class="mb-10">
					{$aLang.plugin.simplecatalog.Schemes.Add.description}
				</div>
				<div>
					<textarea name="description" class="input-text input-width-full mce-editor markitup-editor" maxlength="2000">{$_aRequest.description}</textarea>
				</div>
			</div>


			<div class="mb-20">
				<div class="mb-10">
					{$aLang.plugin.simplecatalog.Schemes.Add.keywords}
				</div>
				<div>
					<input type="text" name="keywords" value="{$_aRequest.keywords}" class="input-text input-width-full" placeholder="{$aLang.plugin.simplecatalog.Schemes.Add.keywords_ph}" />
				</div>
			</div>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_general}</h2>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.active}</dt>
				<dd>
					<select name="active" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.active==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.active==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.menu_add_topic_create}</dt>
				<dd>
					<select name="menu_add_topic_create" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.menu_add_topic_create==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.menu_add_topic_create==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.menu_main_add_link}</dt>
				<dd>
					<select name="menu_main_add_link" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.menu_main_add_link==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.menu_main_add_link==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.short_view_fields_count}</dt>
				<dd>
					<input type="text" name="short_view_fields_count" value="{$_aRequest.short_view_fields_count}" class="input-text input-width-250" placeholder="2" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.allow_comments}</dt>
				<dd>
					<select name="allow_comments" class="input-text input-width-250">
						{foreach from=array($SC_ALLOW_COMMENTS_FORCED_TO_ALLOW,$SC_ALLOW_COMMENTS_DENY,$SC_ALLOW_COMMENTS_USER_DEFINED) item=sValue}
							<option value="{$sValue}" {if $_aRequest.allow_comments==$sValue}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Schemes.Add.allow_comments_type.$sValue}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_seo}</h2>


			<div class="mb-20">{$aLang.plugin.simplecatalog.Schemes.Add.seo_section_description}</div>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.allow_user_friendly_url}</dt>
				<dd>
					<select name="allow_user_friendly_url" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.allow_user_friendly_url==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.allow_user_friendly_url==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.allow_edit_additional_seo_meta}</dt>
				<dd>
					<select name="allow_edit_additional_seo_meta" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.allow_edit_additional_seo_meta==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.allow_edit_additional_seo_meta==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_other}</h2>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.moderation_needed}</dt>
				<dd>
					<select name="moderation_needed" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.moderation_needed==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.moderation_needed==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.show_first_letter_groups}</dt>
				<dd>
					<select name="show_first_letter_groups" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.show_first_letter_groups==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.show_first_letter_groups==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.profile_show_last_products}</dt>
				<dd>
					<select name="profile_show_last_products" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.profile_show_last_products==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.profile_show_last_products==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.profile_show_created_products}</dt>
				<dd>
					<select name="profile_show_created_products" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.profile_show_created_products==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.profile_show_created_products==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.show_online_comments}</dt>
				<dd>
					<select name="show_online_comments" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.show_online_comments==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.show_online_comments==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.sorting}</dt>
				<dd>
					<input type="text" name="sorting" value="{$_aRequest.sorting}" class="input-text input-width-250" placeholder="1" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.items_per_page}</dt>
				<dd>
					<input type="text" name="items_per_page" value="{$_aRequest.items_per_page}" class="input-text input-width-250" placeholder="15" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.what_to_show_on_items_page}</dt>
				<dd>
					<select name="what_to_show_on_items_page" class="input-text input-width-250">
						{foreach from=array($SC_SHOW_ON_ITEMS_PAGE_LAST_PRODUCTS, $SC_SHOW_ON_ITEMS_PAGE_CATEGORIES, $SC_SHOW_ON_ITEMS_PAGE_MAP) item=sValue}
							<option value="{$sValue}" {if $_aRequest.what_to_show_on_items_page==$sValue}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Schemes.Add.what_to_show_on_items_page_type.$sValue}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.block_show_last_products}</dt>
				<dd>
					<select name="block_show_last_products" class="input-text input-width-250">
						{foreach from=array(
							PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_NONE,
							PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_BEFORE_CONTENT,
							PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_IN_SIDEBAR
						) item=sValue}
							<option value="{$sValue}" {if $_aRequest.block_show_last_products==$sValue}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.common.show_products_block_place_type.$sValue}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.allow_drafts}</dt>
				<dd>
					<select name="allow_drafts" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.allow_drafts==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.allow_drafts==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.allow_deferred_products}</dt>
				<dd>
					<select name="allow_deferred_products" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.allow_deferred_products==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.allow_deferred_products==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.allow_count_views}</dt>
				<dd>
					<select name="allow_count_views" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.allow_count_views==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.allow_count_views==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_templates}</h2>


			<div class="mb-20">{$aLang.plugin.simplecatalog.Schemes.Add.templates_section_description}</div>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.template_name_first}</dt>
				<dd>
					<select name="template_name_first" class="input-text input-width-250">
						{foreach $_aRequest.scheme_template_names as $sCode => $sName}
							<option value="{$sCode}" {if $_aRequest.template_name_first==$sCode}selected="selected"{/if}>
								{$sName}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.template_name_second}</dt>
				<dd>
					<select name="template_name_second" class="input-text input-width-250">
						{foreach $_aRequest.scheme_template_names as $sCode => $sName}
							<option value="{$sCode}" {if $_aRequest.template_name_second==$sCode}selected="selected"{/if}>
								{$sName}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.use_first_template_as_default}</dt>
				<dd>
					<select name="use_first_template_as_default" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.use_first_template_as_default==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.use_first_template_as_default==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_product_images}</h2>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.max_images_count}</dt>
				<dd>
					<input type="text" name="max_images_count" value="{$_aRequest.max_images_count}" class="input-text input-width-250" placeholder="5" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.image_width}</dt>
				<dd>
					<input type="text" name="image_width" value="{$_aRequest.image_width}" class="input-text input-width-250" placeholder="600" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.image_height}</dt>
				<dd>
					<input type="text" name="image_height" value="{$_aRequest.image_height}" class="input-text input-width-250" placeholder="400" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.exact_image_proportions}</dt>
				<dd>
					<select name="exact_image_proportions" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.exact_image_proportions==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Schemes.Add.exact_image_proportions_types.exact_crop}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.exact_image_proportions==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Schemes.Add.exact_image_proportions_types.one_side_fit}
						</option>
					</select>
				</dd>
			</dl>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_shop}</h2>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.shop}</dt>
				<dd>
					<select name="shop" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.shop==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.shop==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_map}</h2>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.map_items}</dt>
				<dd>
					<select name="map_items" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.map_items==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.map_items==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.select_preset_for_map_items}</dt>
				<dd>
					<select name="select_preset_for_map_items" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.select_preset_for_map_items==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.select_preset_for_map_items==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.map_items_max}</dt>
				<dd>
					<input type="text" name="map_items_max" value="{$_aRequest.map_items_max}" class="input-text input-width-250" placeholder="15" />
				</dd>
			</dl>


			<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Schemes.Add.title_access}</h2>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.can_add_products}</dt>
				<dd>
					<select name="can_add_products" class="input-text input-width-250">
						{foreach from=array($SC_CAN_ADD_PRODUCTS_ADMINS,$SC_CAN_ADD_PRODUCTS_ANY_USER) item=sValue}
							<option value="{$sValue}" {if $_aRequest.can_add_products==$sValue}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Schemes.Add.can_add_products_type.$sValue}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.min_user_rating_to_create_products}</dt>
				<dd>
					<input type="text" name="min_user_rating_to_create_products" value="{$_aRequest.min_user_rating_to_create_products}" class="input-text input-width-250" placeholder="0" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Schemes.Add.days_author_can_manage_products_after_last_editing}</dt>
				<dd>
					<input type="text" name="days_author_can_manage_products_after_last_editing" value="{$_aRequest.days_author_can_manage_products_after_last_editing}" class="input-text input-width-250" placeholder="0" />
				</dd>
			</dl>


			{hook run='sc_scheme_add_end'}


			<input type="submit" value="{$aLang.plugin.simplecatalog.Schemes.Add.submit_title}" name="submit_add" class="button button-primary" />
		</form>
	</div>

{include file='footer.tpl'}