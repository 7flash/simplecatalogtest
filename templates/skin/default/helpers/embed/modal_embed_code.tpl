
{*
	Модальное окно получения ембед кода продуктов
*}

{*
	tip: часть кода будет размещена здесь т.к. в дев версии другие модальные окна и этот код будет нуждается в переделке
*}
<script>
	jQuery (document).ready (function ($) {
		/**
		 * подключение модального окна
		 */
		$ ('#js-sc-embed-code-modal').jqm ();
	});
</script>

<div id="js-sc-embed-code-modal" class="modal Simplecatalog" style="margin-left: -350px; width: 700px; top: 10%;">
	<header class="modal-header">
		<h3>{$aLang.plugin.simplecatalog.embed_code.modal.title}</h3>
		<a href="#" class="close jqmClose"></a>
	</header>

	<div class="modal-content">
		<div class="product-embed-code">
			<textarea id="js-sc-embed-code-wrapper" class="input-text input-width-full mb-20" onclick="$ (this).select();" readonly="readonly"></textarea>

			<div class="mb-20">
				{$aLang.plugin.simplecatalog.embed_code.modal.will_look_like_this}:
			</div>
			<div id="js-sc-embed-live-preview"></div>
		</div>
	</div>
</div>
