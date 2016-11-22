{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.schemes_fields.tpl"}

	<div class="Simplecatalog Field Add">
		<h2 class="page-header title-underline">
			{if $_aRequest.id}
				{$aLang.plugin.simplecatalog.Fields.Add.titles.edit}
			{else}
				{$aLang.plugin.simplecatalog.Fields.Add.titles.new}
			{/if}
			"<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>"
		</h2>
		{* Добавить поле к схеме *}

		<form action="{router page='field'}add" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />

			<input type="hidden" name="id" value="{$_aRequest.id}" />
			<input type="hidden" name="scheme_id" value="{$_aRequest.scheme_id}" />

			<div class="mb-20">

				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.title}</dt>
					<dd>
						<input type="text" name="title" value="{$_aRequest.title}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Fields.Add.title_ph}" />
					</dd>
				</dl>


				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.description}</dt>
					<dd>
						<input type="text" name="description" value="{$_aRequest.description}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Fields.Add.description_ph}" />
					</dd>
				</dl>


				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.mandatory}</dt>
					<dd>
						<select name="mandatory" class="input-text input-width-250">
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.mandatory==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Yes}
							</option>
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.mandatory==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.No}
							</option>
						</select>
					</dd>
				</dl>


				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.code}</dt>
					<dd>
						<input type="text" name="code" value="{$_aRequest.code}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Fields.Add.code_ph}" />
					</dd>
				</dl>



				{* Тип поля и настройки типа *}
				<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Fields.Add.title_field_type}</h2>



				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.field_type}</dt>
					<dd>
						<select name="field_type" class="input-text input-width-250">
							{foreach from=Config::Get("plugin.simplecatalog.scheme.Allowed_Field_Types") item=sValue}
								<option value="{$sValue}" {if $_aRequest.field_type==$sValue}selected="selected"{/if}>
									{$aLang.plugin.simplecatalog.Fields.Add.field_types.$sValue}
								</option>
							{/foreach}
						</select>
					</dd>
				</dl>



				{* Типы полей *}


				<dl class="w50p mb-5 FieldType_text">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.text_min_length}</dt>
					<dd>
						<input type="text" name="text_min_length" value="{$_aRequest.text_min_length}" class="input-text input-width-250" placeholder="2" />
					</dd>
				</dl>

				<dl class="w50p mb-5 FieldType_text">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.text_max_length}</dt>
					<dd>
						<input type="text" name="text_max_length" value="{$_aRequest.text_max_length}" class="input-text input-width-250" placeholder="2000" />
					</dd>
				</dl>


				<dl class="w50p mb-5 FieldType_textarea">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.textarea_min_length}</dt>
					<dd>
						<input type="text" name="textarea_min_length" value="{$_aRequest.textarea_min_length}" class="input-text input-width-250" placeholder="2" />
					</dd>
				</dl>

				<dl class="w50p mb-5 FieldType_textarea">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.textarea_max_length}</dt>
					<dd>
						<input type="text" name="textarea_max_length" value="{$_aRequest.textarea_max_length}" class="input-text input-width-250" placeholder="5000" />
					</dd>
				</dl>


				<dl class="w50p mb-5 FieldType_file">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.file_max_size}</dt>
					<dd>
						<input type="text" name="file_max_size" value="{$_aRequest.file_max_size}" class="input-text input-width-250" placeholder="500" />
					</dd>
				</dl>

				<dl class="w50p mb-5 FieldType_file">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.file_types_allowed}</dt>
					<dd>
						<input type="text" name="file_types_allowed" value="{$_aRequest.file_types_allowed}" class="input-text input-width-250" placeholder="png, jpg, zip" />
					</dd>
				</dl>


				<dl class="w50p mb-5 FieldType_select">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.select_items}</dt>
					<dd>
						<textarea name="select_items" class="input-text input-width-250">{$_aRequest.select_items}</textarea>
					</dd>
				</dl>

				<dl class="w50p mb-5 FieldType_select">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.select_multiple_items}</dt>
					<dd>
						<select name="select_multiple_items" class="input-text input-width-250">
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.select_multiple_items==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Yes}
							</option>
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.select_multiple_items==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.No}
							</option>
						</select>
					</dd>
				</dl>

				<dl class="w50p mb-5 FieldType_select">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.select_filter_items_using_and_logic}</dt>
					<dd>
						<select name="select_filter_items_using_and_logic" class="input-text input-width-250">
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.select_filter_items_using_and_logic==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Fields.Add.select_filter_items_processing.logic_and}
							</option>
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.select_filter_items_using_and_logic==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Fields.Add.select_filter_items_processing.logic_or}
							</option>
						</select>
					</dd>
				</dl>


				{* /Типы полей *}



				{* Обработка значения поля *}
				<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Fields.Add.title_field_text_proccessing}</h2>



				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.run_parser}</dt>
					<dd>
						<select name="run_parser" class="input-text input-width-250">
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.run_parser==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Yes}
							</option>
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.run_parser==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.No}
							</option>
						</select>
					</dd>
				</dl>


				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.validator}</dt>
					<dd>
						<select name="validator" class="input-text input-width-250">
							<option value="" {if $_aRequest.validator==''}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.validator_is_not_needed}
							</option>
							{foreach from=Config::Get("plugin.simplecatalog.validators.list") key=sKey item=aValue}
								<option value="{$sKey}" {if $_aRequest.validator==$sKey}selected="selected"{/if}>
									{$aLang.plugin.simplecatalog.validators_list.$sKey.name}
								</option>
							{/foreach}
						</select>
					</dd>
				</dl>


				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.default_value}</dt>
					<dd>
						<input type="text" name="default_value" value="{$_aRequest.default_value}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Fields.Add.default_value_ph}" />
					</dd>
				</dl>



				{* Визуальное оформление *}
				<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Fields.Add.title_visual_data}</h2>



				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.places_to_show_field}</dt>
					<dd>
						<select name="places_to_show_field" class="input-text input-width-250">
							{foreach from=array($SC_FIELD_SHOW_ANYWHERE,$SC_FIELD_SHOW_IN_PRODUCT_LIST,$SC_FIELD_SHOW_ON_PRODUCT_PAGE,$SC_FIELD_SHOW_NOWHERE) item=sValue}
								<option value="{$sValue}" {if $_aRequest.places_to_show_field==$sValue}selected="selected"{/if}>
									{$aLang.plugin.simplecatalog.Fields.Add.places_list.$sValue}
								</option>
							{/foreach}
						</select>
					</dd>
				</dl>

				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.value_prefix}</dt>
					<dd>
						<input type="text" name="value_prefix" value="{$_aRequest.value_prefix}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Fields.Add.value_prefix_ph}" />
					</dd>
				</dl>

				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.value_postfix}</dt>
					<dd>
						<input type="text" name="value_postfix" value="{$_aRequest.value_postfix}" class="input-text input-width-250" placeholder="{$aLang.plugin.simplecatalog.Fields.Add.value_postfix_ph}" />
					</dd>
				</dl>


				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.sorting}</dt>
					<dd>
						<input type="text" name="sorting" value="{$_aRequest.sorting}" class="input-text input-width-250" placeholder="1" />
					</dd>
				</dl>


				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.show_field_names_in_list}</dt>
					<dd>
						<select name="show_field_names_in_list" class="input-text input-width-250">
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.show_field_names_in_list==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Yes}
							</option>
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.show_field_names_in_list==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.No}
							</option>
						</select>
					</dd>
				</dl>



				{* Доступ к полю *}
				<h2 class="page-header title-underline">{$aLang.plugin.simplecatalog.Fields.Add.title_access}</h2>



				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.allow_search_in_this_field}</dt>
					<dd>
						<select name="allow_search_in_this_field" class="input-text input-width-250">
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.allow_search_in_this_field==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Yes}
							</option>
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.allow_search_in_this_field==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.No}
							</option>
						</select>
					</dd>
				</dl>

				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.for_auth_users_only}</dt>
					<dd>
						<select name="for_auth_users_only" class="input-text input-width-250">
							{*
								tip: изменен порядок вариантов "Да/Нет" для значения по-умолчанию
							*}
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.for_auth_users_only==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.No}
							</option>
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.for_auth_users_only==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Yes}
							</option>
						</select>
					</dd>
				</dl>

				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.min_user_rating_to_view}</dt>
					<dd>
						<input type="text" name="min_user_rating_to_view" value="{$_aRequest.min_user_rating_to_view}" class="input-text input-width-250" placeholder="0" />
					</dd>
				</dl>

				<dl class="w50p mb-5">
					<dt>{$aLang.plugin.simplecatalog.Fields.Add.editable_by_user}</dt>
					<dd>
						<select name="editable_by_user" class="input-text input-width-250">
							<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.editable_by_user==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.Yes}
							</option>
							<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.editable_by_user==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
								{$aLang.plugin.simplecatalog.No}
							</option>
						</select>
					</dd>
				</dl>

			</div>

			<input type="submit" value="{$aLang.plugin.simplecatalog.Fields.Add.submit_title}" name="submit_add" class="button button-primary" />
		</form>
	</div>

{include file='footer.tpl'}