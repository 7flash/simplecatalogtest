
	<dl class="fields-wrapper">
		<dt>
			<i class="sc-icon-question-sign js-tip-help fl-r"
			   title="{$aLang.plugin.simplecatalog.common.from_M_to_N_symbols|ls_lang:"m%%`$oField->getTextMinLength()`":"n%%`$oField->getTextMaxLength()`"}"></i>
			{$oField->getTitle()}

			{$mValuePrefix = $oField->getValuePrefix()}
			{$mValuePostfix = $oField->getValuePostfix()}
			{if $mValuePrefix or $mValuePostfix}({$mValuePrefix}{if $mValuePrefix and $mValuePostfix}, {/if}{$mValuePostfix}){/if}

			{if $oField->getMandatoryEnabled()}
				<b>*</b>
			{/if}
		</dt>
		<dd>
			<input type="text" name="{$sName}" value="{$sValue}"
				   class="input-text input-width-full {if $oField->getValidatorTypeIsDate()}js-date-picker-php{/if}"
				   maxlength="{$oField->getTextMaxLength()}" />

			<span class="note">
				{$oField->getDescription()}
			</span>
		</dd>
	</dl>
