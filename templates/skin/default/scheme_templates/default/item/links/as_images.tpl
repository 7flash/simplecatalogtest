
{*
	Связи продукта - отображение изображениями
*}

{sc_scheme_template scheme=$oScheme file="item/links/helpers/after_fields_items.tpl"
	aProductLinksData = $oProduct->getProductLinksDataWithShowTypeAsImages()
	sLinkItemType = 'image'
}
