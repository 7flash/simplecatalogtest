
{*
	Общая карта с метками всех продуктов схемы (загрузка меток по мере необходимости)
*}

{*
	враппер для карты
*}
<div id="sc_products_map_wrapper" class="mb-10"></div>

<script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>
	jQuery (document).ready (function ($) {
		/**
		 * создать просмотр карты
		 */
		ls.sc_map_editor_yandex.BuildMapWithControls('sc_products_map_wrapper', {
			aCenter: [{Config::Get('plugin.simplecatalog.maps.settings.base.center')}],
			iZoom: {Config::Get('plugin.simplecatalog.maps.settings.base.zoom')},
			bDisableEvents: true
		});
		/**
		 * подключить загружающий менеджер объектов
		 */
		ls.sc_map_editor_yandex.AddLoadingObjectManagerOnMap({
			scheme_id: {$oScheme->getId()},
			security_ls_key: LIVESTREET_SECURITY_KEY
		});
		/**
		 * добавить метку "Я" с координатами пользователя
		 */
		ls.sc_map_editor_yandex.SetPointToUsersCoords();
	});
</script>
