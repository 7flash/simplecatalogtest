
{*
	Модальное окно корзины
*}


<div class="modal fade Simplecatalog" tabindex="-1" role="dialog" id="js-sc-cart">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">{$aLang.plugin.simplecatalog.shop.cart.title}</h4>
			</div>
			<div class="modal-body">
				<div id="js-sc-cart-items-wrapper"></div>
			</div>
			<div class="modal-footer">
				<a href="{router page='shop'}order" class="btn btn-primary" rel="nofollow">{$aLang.plugin.simplecatalog.shop.cart.order_items}</a>
				&nbsp;
				<a href="#" class="btn btn-default" data-dismiss="modal">{$aLang.plugin.simplecatalog.shop.cart.continue_shopping}</a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->