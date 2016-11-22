
<h2>Редактирование проекта</h2>

{* *********** иконки статуса *********** *}
<div style="margin-bottom: 40px;">
	{if $_aRequest.moderation_done}
		Статус проекта: <span title="{$aLang.plugin.simplecatalog.Products.Items.moderation_done}" class="label label-success">{$aLang.plugin.simplecatalog.Products.Items.moderation_done}</span>
	{/if}
	{if $_aRequest.moderation_needed}
		Статус проекта: <span title="{$aLang.plugin.simplecatalog.Products.Items.on_moderation}" class="label label-warning">{$aLang.plugin.simplecatalog.Products.Items.on_moderation}</span>
	{/if}
	{if $_aRequest.moderation_saved_as_draft}
		Статус проекта: <span title="{$aLang.plugin.simplecatalog.Products.Items.saved_as_draft}" class="label label-default">{$aLang.plugin.simplecatalog.Products.Items.saved_as_draft}</span>
	{/if}

	{if $_aRequest.item_show_web_path}
		{if $_aRequest.moderation_done}
			<a class="js_c_link_detail_page pull-right btn btn-info btn-sm" href="{$_aRequest.item_show_web_path}" target="_blank">На страницу проекта</a>
		{else}
			<a class="js_c_link_detail_page pull-right btn btn-default btn-sm" href="{$_aRequest.item_show_web_path}" target="_blank"><span class="glyphicon glyphicon-eye-open"></span>  Предпросмотр</a>
		{/if}
	{/if}
</div>

{* // TODO - отключить редактор  *}
{* редактор *}
{* {include file='editor.tpl'} *}

{* // TODO - открыть  *}
{* js обработка формы *}
{* {include file="{$aTemplatePathPlugin.simplecatalog}actions/ActionProduct/project_addjs.tpl"}  *}

<div class="alert alert-success js_c_submit_mes_success top" style="display: none;">&nbsp;</div>

<div class="alert alert-danger js_c_submit_mes_error top" style="display: none;">&nbsp;</div>

{* // TODO - открыть  *}
{literal} {/literal}
<script>

	var GF_PAGE = "project_edit";

	var GF_EDITOR_IMG_WIDTH_MAX = {cfg name="getfunded.editor_img_width_max"};

	var GF_PROJECT_ID = {$_aRequest.id};

	$(document).ready(function(){
		// $(".tabs").lightTabs();
	});

</script>


<div class="tabs">

	<ul class="nav nav-tabs">
		<li role="presentation" class="js_c_tab_li active"><a class="js_c_tab_link" href="#">Основные данные</a></li>
		<li role="presentation" class="js_c_tab_li"><a class="js_c_tab_link" href="#">Детали</a></li>
		<li role="presentation" class="js_c_tab_li"><a class="js_c_tab_link" href="#">Контрагент</a></li>
	</ul>

	<div class="tab_content_wrapper">

		<!-- //////////////////// START TAB - MAIN ////////////////////////// -->
		<div class="tab_content main">

			<div class="form_edit_wrapper">
				<div class="row">
					<div class="col-md-8">

						<!-- //////////////////// START - FILE UPLOAD ////////////////////// -->

						{if $_aRequest.id and $oScheme->getMaxImagesCount()}
							{include file="{$aTemplatePathPlugin.simplecatalog}helpers/images/project_add_images.tpl"
								iTargetId = $_aRequest.id
								iTargetType = PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS
								iMaxImagesCount = $oScheme->getMaxImagesCount()
								sText = $aLang.plugin.simplecatalog.Products.Add.images_of_product
								arProjectImages = $arProjectImages
							}
						{/if}
						<!-- //////////////////// END - FILE UPLOAD ////////////////////// -->




						<!-- //////////////////// START - FORM ////////////////////// -->
						<form class="js_c_form_main js_c_form_edit form-horizontal" action="{router page='product'}save/{$oScheme->getSchemeUrl()}" method="post" enctype="multipart/form-data">
							<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
							<input type="hidden" name="id" value="{$_aRequest.id}" />

							{* для сохранения статусов и ссылки на продукт если при отправке формы не прошла валидация *}
							<input type="hidden" name="moderation_needed" value="{$_aRequest.moderation_needed}" />
							<input type="hidden" name="moderation_saved_as_draft" value="{$_aRequest.moderation_saved_as_draft}" />
							<input type="hidden" name="moderation_saved_as_deferred" value="{$_aRequest.moderation_saved_as_deferred}" />
							<input type="hidden" name="item_show_web_path" value="{$_aRequest.item_show_web_path}" />

							<input type="hidden" name="form_tab_code" value="main" />


							{* Вывод полей продукта для редактирования *}
							{foreach from=$oScheme->getFields() item=oField}

								{if !isset($arProjectFormTabs["main"][$oField->getCode()])}
									{continue}
								{/if}

								{*} {$var|@p} {*}

								{* не выводить поля, которые не заполняются через форму *}
								{if !$oField->getEditableByUserEnabled()}{continue}{/if}

								{* "product_data" - массив для хранения значений и удобного доступа к ним *}
								{assign var="sName" value="product_data[{$oField->getId()}]"}		 {* должен быть массивом *}
								{assign var="sValue" value=$_aRequest['product_data'][$oField->getId()]} {* только так получать данные *}

								{* задать значение по-умолчанию только для новых продуктов *}
								{if $sValue === null and !$_aRequest.id}
									{assign var="sValue" value=$oField->getDefaultValue()}
								{/if}

								{sc_scheme_template scheme=$oScheme file="add/fields/project/{$oField->getFieldType()}.tpl"}

							{/foreach}

							{* URL Страница проекта *}
							<div class="form-group">
								<label class="col-md-4 control-label">Адрес проекта <span class="color_red fwb">*</span>
									<i class="sc-icon-question-sign js-tip-help fl-r"
									   title="Допустимо использовать только латинские буквы, цифры, знак подчеркивания (_), тире (-).<br> Длина от 3-х символов."></i>
								</label>
								<div class="control-field">
									<div class="col-md-3" style="padding: 6px 0px 0px; font-size: 12px; width: 148px;">
										{Config::Get('path.root.web')}{Config::Get('getfunded.path.page_project_index')}
									</div>
									<div class="col-md-5" style="padding-left: 2px;">
										<input class="js_c_field_product_url form-control input-sm col-md-3 input-text input-width-full" type="text" name="product_url" value="{$_aRequest.product_url}" maxlength="100" />
										<div class="js_c_err_product_url js_c_sub_field_err"></div>
									</div>
								</div>
							</div><!-- class="form-group" -->

							{* категории продукта *}
							{if Config::Get('plugin.simplecatalog.categories.show_block_when_no_categories') or ($aCategoryTree and count($aCategoryTree)>0)}

								<div class="form-group">
									<label class="col-md-4 control-label">Категория проекта <span class="color_red fwb">*</span></label>
									<div class="control-field col-md-7">
										{include file="{$aTemplatePathPlugin.simplecatalog}helpers/categories/project_select.tpl"
										sName="categories_ids" sNoCategoryText="" aCategoryTree=$aCategoryTree
										}
									</div>
								</div><!-- class="form-group" -->
							{/if}


							<div class="control-group"><div class="js_c_save_error main">&nbsp;</div></div>

							{* Кнопки публикации продукта *}
							<!--input type="submit" value="{$aLang.plugin.simplecatalog.Products.Add.submit_title}" name="submit_add" class="button button-primary" /-->
							{* если в схеме разрешены черновики *}
							{if $oScheme->getAllowDraftsEnabled()}
								<!--input type="submit" value="{$aLang.plugin.simplecatalog.Products.Add.save_draft}" name="submit_save_draft" class="button" /-->

								<div style="display: block;">
									<input type="hidden" name="submit_save_draft" value="Y" />
									<button type="submit" class="btn btn-primary btn-sm js_c_button_save_changes main" data-tab_name="main">Сохранить</button>
									<img class="js_c_step_loader main" src="{cfg name="path.static.skin"}/images/project/loader.gif" width="18" height="18" />
								</div>
							{/if}

						</form>
						<!-- //////////////////// END - FORM ////////////////////// -->

					</div> <!-- class="col-md-8" -->
				</div><!-- class="row" -->
			</div> <!-- class="form_edit_wrapper" -->

		</div> <!-- tab_content main -->
		<!-- //////////////////// END TAB - MAIN ////////////////////////// -->

		<!-- //////////////////// START TAB - DETAIL ////////////////////////// -->
		<div>

			<div class="form_edit_wrapper">
				<div class="row">
					<div class="col-md-8">

						<!-- //////////////////// START - FORM ////////////////////// -->
						<form class="js_c_form_detail js_c_form_edit form-horizontal" action="{router page='product'}save/{$oScheme->getSchemeUrl()}" method="post" enctype="multipart/form-data">
							<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
							<input type="hidden" name="id" value="{$_aRequest.id}" />

							{* для сохранения статусов и ссылки на продукт если при отправке формы не прошла валидация *}
							<input type="hidden" name="moderation_needed" value="{$_aRequest.moderation_needed}" />
							<input type="hidden" name="moderation_saved_as_draft" value="{$_aRequest.moderation_saved_as_draft}" />
							<input type="hidden" name="moderation_saved_as_deferred" value="{$_aRequest.moderation_saved_as_deferred}" />
							<input type="hidden" name="item_show_web_path" value="{$_aRequest.item_show_web_path}" />

							<input type="hidden" name="form_tab_code" value="detail" />

							{* Вывод полей продукта для редактирования *}
							{foreach from=$oScheme->getFields() item=oField}

								{if !isset($arProjectFormTabs["detail"][$oField->getCode()])}
									{continue}
								{/if}

								{* не выводить поля, которые не заполняются через форму *}
								{if !$oField->getEditableByUserEnabled()}{continue}{/if}

								{* "product_data" - массив для хранения значений и удобного доступа к ним *}
								{assign var="sName" value="product_data[{$oField->getId()}]"}		 {* должен быть массивом *}
								{assign var="sValue" value=$_aRequest['product_data'][$oField->getId()]} {* только так получать данные *}

								{* задать значение по-умолчанию только для новых продуктов *}
								{if $sValue === null and !$_aRequest.id}
									{assign var="sValue" value=$oField->getDefaultValue()}
								{/if}

								{sc_scheme_template scheme=$oScheme file="add/fields/project/{$oField->getFieldType()}.tpl"}

							{/foreach}

							{* если в схеме разрешены черновики *}
							{if $oScheme->getAllowDraftsEnabled()}
								<div style="display: block;">
									<input type="hidden" name="submit_save_draft" value="Y" />
									<button type="submit" class="btn btn-primary btn-sm js_c_button_save_changes detail" data-tab_name="detail">Сохранить</button>
									<img class="js_c_step_loader detail" src="{cfg name="path.static.skin"}/images/project/loader.gif" width="18" height="18" />
								</div>
							{/if}

						</form>
						<!-- //////////////////// END - FORM ////////////////////// -->

					</div> <!-- class="col-md-8" -->
				</div><!-- class="row" -->
			</div> <!-- class="form_edit_wrapper" -->

		</div><!-- tab_content detail -->
		<!-- //////////////////// END TAB - MAIN ////////////////////////// -->

		<!-- //////////////////// START TAB - AGENT ////////////////////////// -->
		<div>Форма Контрагент</div>
		<!-- //////////////////// END TAB - MAIN ////////////////////////// -->
	</div>
</div>

