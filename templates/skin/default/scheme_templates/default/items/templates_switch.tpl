
{*
	Кнопки переключения шаблонов схемы

	Передаваемые переменные

		oScheme - сущность схемы
*}

<div class="logic-item pull-right">
	<ul class="nav nav-pills">
		{*
			первый шаблон
		*}
		<li class="switch-item{if $oScheme->getFirstTemplateIsCurrent()} active{/if}">
			<a href="{$oScheme->getChangeToFirstTemplateWebPath()}" class="switch-link glyphicon glyphicon glyphicon-th"
			   rel="nofollow" title="{$aLang.plugin.simplecatalog.scheme_template_names.user.{$oScheme->getTemplateNameFirst()}}"></a>
		</li>
		{*
			второй шаблон
		*}
		<li class="switch-item{if $oScheme->getSecondTemplateIsCurrent()} active{/if}">
			<a href="{$oScheme->getChangeToSecondTemplateWebPath()}" class="switch-link glyphicon glyphicon-th-list"
			   rel="nofollow" title="{$aLang.plugin.simplecatalog.scheme_template_names.user.{$oScheme->getTemplateNameSecond()}}"></a>
		</li>
	</ul>
</div>
