
<div class="mb-5">
	<i class="sc-icon-question-sign fl-r js-tip-help"
	   title="{$aLang.plugin.simplecatalog.common.from_M_to_N_symbols|ls_lang:"m%%`$oField->getTextareaMinLength()`":"n%%`$oField->getTextareaMaxLength()`"}"></i>

	{$oField->getTitle()}

	{$mValuePrefix = $oField->getValuePrefix()}
	{$mValuePostfix = $oField->getValuePostfix()}
	{if $mValuePrefix or $mValuePostfix}({$mValuePrefix}{if $mValuePrefix and $mValuePostfix}, {/if}{$mValuePostfix}){/if}

	{if $oField->getMandatoryEnabled()}
		<b>*</b>
	{/if}
</div>
<div>
	<textarea name="{$sName}" class="input-text input-width-full multi-line-editor mce-editor markitup-editor" maxlength="{$oField->getTextareaMaxLength()}">{$sValue}</textarea>
		<span class="note">
			{$oField->getDescription()}
		</span>
</div>
