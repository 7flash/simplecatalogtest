
{*
	Описание поля в появляющейся иконке
*}

{if $sFieldDescription = $oField->getDescription()}
	<div class="filter-field-help"><i class="sc-icon-info-sign js-tip-help" title="{$sFieldDescription|escape}"></i></div>
{/if}
