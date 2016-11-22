
{*
	Кнопки переключения шаблонов схемы

	Передаваемые переменные

		oScheme - сущность схемы
*}

<div class="logic-item">
	<ul class="switch-group">
		{*
			первый шаблон
		*}
		<li class="switch-item{if $oScheme->getFirstTemplateIsCurrent()} active{/if}">
			<a href="{$oScheme->getChangeToFirstTemplateWebPath()}" class="switch-link view-type-{$oScheme->getTemplateNameFirst()}"
			   rel="nofollow" title="{$aLang.plugin.simplecatalog.scheme_template_names.user.{$oScheme->getTemplateNameFirst()}}"></a>
		</li>
		{*
			второй шаблон
		*}
		<li class="switch-item{if $oScheme->getSecondTemplateIsCurrent()} active{/if}">
			<a href="{$oScheme->getChangeToSecondTemplateWebPath()}" class="switch-link view-type-{$oScheme->getTemplateNameSecond()}"
			   rel="nofollow" title="{$aLang.plugin.simplecatalog.scheme_template_names.user.{$oScheme->getTemplateNameSecond()}}"></a>
		</li>
	</ul>
</div>
