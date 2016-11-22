
{*
	Модальное окно корзины
*}

{*
	tip: часть кода будет размещена здесь т.к. в дев версии другие модальные окна и этот код будет нуждается в переделке
*}
<script>
	jQuery (document).ready (function ($) {
		/**
		 * подключение модального окна
		 */
		$ ('#js-sc-cart').jqm ();
	});
</script>

<div id="js-sc-cart" class="modal Simplecatalog" style="margin-left: -450px; width: 900px;">
	<header class="modal-header">
		<h3>{$aLang.plugin.simplecatalog.shop.cart.title}</h3>
		<a href="#" class="close jqmClose"></a>
	</header>

	<div class="modal-content">
		{*
			список продуктов в корзине
		*}
		<div id="js-sc-cart-items-wrapper"></div>
		{*
			кнопки продолжения покупок или оформления заказа
		*}
		<div class="mt15">
			<a href="{router page='shop'}order" class="button button-primary" rel="nofollow">{$aLang.plugin.simplecatalog.shop.cart.order_items}</a>
			&nbsp;
			<a href="#" class="button jqmClose">{$aLang.plugin.simplecatalog.shop.cart.continue_shopping}</a>
		</div>
	</div>
</div>
