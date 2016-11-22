
{*
	Связи продукта - отображение в селекте (выпадающий список)
*}

{capture name="links_prepend_text"}
	<select class="input-text input-width-250 js-sc-select-url">
		<option value="">---</option>
{/capture}

{capture name="links_append_text"}
	</select>
{/capture}

{sc_scheme_template scheme=$oScheme file="item/links/helpers/after_fields_items.tpl"
	aProductLinksData = $oProduct->getProductLinksDataWithShowTypeInSelect()
	sLinkItemType = 'select'
	sLinksListPrependText = $smarty.capture.links_prepend_text
	sLinksListAppendText = $smarty.capture.links_append_text
}
