
{*
	Значение поля продукта

	Передаваемые параметры

		oField - поле схемы
		sValue - отображаемое значение поля продукта
		oProductField - поле продукта
		oProduct - продукт
		oScheme - схема
*}

{*
	нужна ли авторизация на сайте для доступа к содержимому поля
*}
{if $oField->getForAuthUsersOnlyEnabled() and !$oUserCurrent}
	<a class="js-login-form-show" href="{router page='login'}">{$aLang.plugin.simplecatalog.Products.Item.values.auth_needed_to_access}</a>

{*
	проверка минимального рейтинга для доступа к полю
*}
{elseif $oField->getForAuthUsersOnlyEnabled() and $oUserCurrent and $oUserCurrent->getRating() < $oField->getMinUserRatingToView() and !$oUserCurrent->getCanManageProduct($oProduct)}
	<i>{$aLang.plugin.simplecatalog.Products.Item.values.you_havent_enough_rating|ls_lang:"rating%%`$oField->getMinUserRatingToView()`"}</i>

{else}
	{*
		вывод значений в зависимости от типа поля
	*}
	{if in_array($oField->getFieldType(), array(
		PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE,
		PluginSimplecatalog_ModuleScheme::FIELD_TYPE_CHECKBOX,
		PluginSimplecatalog_ModuleScheme::FIELD_TYPE_NOT_EDITABLE
	))}
		{*
			специальный вывод, для которого нужна дополнительная шаблонная логика
		*}
		{sc_scheme_template scheme=$oScheme file="item/field_value/{$oField->getFieldType()}.tpl"}
	{else}
		{*
			обычный вывод значения "как есть"
		*}
		{*
			если нет значения и нужно показывать заголовок поля - вывести сообщение что нет значения
		*}
		{if !$sValue and $oField->getShowFieldNamesInListEnabled()}
			{$aLang.plugin.simplecatalog.No}
		{else}
			{$sValue}
		{/if}
	{/if}
{/if}
