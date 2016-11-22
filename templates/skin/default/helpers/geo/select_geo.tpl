
{*
	Гео данные для оформления заказа из корзины
*}

<script>
	jQuery(document).ready(function($) {
		ls.lang.load({lang_load name="geo_select_city,geo_select_region"});
		ls.geo.initSelect();
	});
</script>

<div class="js-geo-select">
	<select class="input-text input-width-250 js-geo-country" name="geo_country">
		<option value="">{$aLang.geo_select_country}</option>
		{if $aGeoCountries}
			{foreach from=$aGeoCountries item=oGeoCountry}
				<option value="{$oGeoCountry->getId()}" {if $_aRequest.geo_country==$oGeoCountry->getId()}selected="selected"{/if}>{$oGeoCountry->getName()}</option>
			{/foreach}
		{/if}
	</select>

	<select class="input-text input-width-250 mt20 js-geo-region" name="geo_region" {if !$_aRequest.geo_country}style="display:none;"{/if}>
		<option value="">{$aLang.geo_select_region}</option>
		{if $aGeoRegions}
			{foreach from=$aGeoRegions item=oGeoRegion}
				<option value="{$oGeoRegion->getId()}" {if $_aRequest.geo_region==$oGeoRegion->getId()}selected="selected"{/if}>{$oGeoRegion->getName()}</option>
			{/foreach}
		{/if}
	</select>

	<select class="input-text input-width-250 mt20 js-geo-city" name="geo_city" {if !$_aRequest.geo_region}style="display:none;"{/if}>
		<option value="">{$aLang.geo_select_city}</option>
		{if $aGeoCities}
			{foreach from=$aGeoCities item=oGeoCity}
				<option value="{$oGeoCity->getId()}" {if $_aRequest.geo_city==$oGeoCity->getId()}selected="selected"{/if}>{$oGeoCity->getName()}</option>
			{/foreach}
		{/if}
	</select>
</div>
