
{*
	Общая карта с метками всех продуктов схемы (загрузка меток по мере необходимости)
*}

{if $oScheme->getMapItemsEnabled()}
	{sc_scheme_template scheme=$oScheme file="maps/all_items_loader/{Config::Get('plugin.simplecatalog.maps.type')}.tpl"}
{else}
	{$aLang.plugin.simplecatalog.Errors.map_items.map_disabled}
{/if}
