
{*
	Список меток на карте для продукта
*}

{if $oScheme->getMapItemsEnabled()}
	{sc_scheme_template scheme=$oScheme file="maps/list/{Config::Get('plugin.simplecatalog.maps.type')}.tpl"}
{/if}
