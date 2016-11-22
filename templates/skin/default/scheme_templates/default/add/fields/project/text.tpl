<div class="form-group">

	{assign var="field_class_column" value="col-md-7"}
	{if $oField->getCode() == "dateuntil" || $oField->getCode() == "sum"}
		{assign var="field_class_column" value="col-md-3 "}
	{/if}

	{assign var="field_tip_text" value="col-md-7"}
	{if $oField->getCode() == "projectname"}
		{assign var="field_tip_text" value="Допустимо использовать только латинские и кириллические буквы, цифры, знак подчеркивания (_), тире (-), кавычки (&quot;), пробел"}
	{elseif $oField->getCode() == "sum"}
		{assign var="field_tip_text" value="Целое число"}
	{elseif $oField->getCode() == "dateuntil"}
		{assign var="field_tip_text" value="Дата в формате YYYY-MM-DD"}
	{/if}

	<label class="col-md-4 control-label">{$oField->getTitle()}
			{if $oField->getCode() == "videolink"}
				{*	<i class="sc-icon-question-sign js-tip-help fl-r"
				   title="{$oField->getDescription()}"></i> *}
			{elseif $oField->getCode() == "previewtext"}
				<i class="sc-icon-question-sign js-tip-help fl-r"
				   title="{$aLang.plugin.simplecatalog.common.from_M_to_N_symbols|ls_lang:"m%%`$oField->getTextMinLength()`":"n%%`$oField->getTextMaxLength()`"}"></i>
			{else}
				<i class="sc-icon-question-sign js-tip-help fl-r"
				   title="{$field_tip_text}"></i>
			{/if}
		{if $oField->getMandatoryEnabled()}
			<span class="color_red fwb">*</span>
		{/if}
	</label>

	<div class="control-field {$field_class_column}">
		{if $oField->getCode() == "previewtext"}
			<textarea class="js_c_field_{$oField->getCode()} form-control input-sm" rows="2" name="{$sName}" maxlength="{$oField->getTextMaxLength()}">{$sValue}</textarea>
		{else}
			<input type="text" name="{$sName}" value="{$sValue}"
			       class="js_c_field_{$oField->getCode()} form-control input-sm input-text input-width-full {if $oField->getValidatorTypeIsDate()}js-date-picker-php{/if}"
			       maxlength="{$oField->getTextMaxLength()}" />
		{/if}

		{*if $oField->getCode() == "videolink"}
		{/if*}
		<span class="note">{$oField->getDescription()}</span>

		<div class="js_c_err_{$oField->getCode()} js_c_sub_field_err"></div>
	</div>

</div><!-- class="form-group" -->