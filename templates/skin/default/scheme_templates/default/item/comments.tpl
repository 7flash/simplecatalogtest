
{*
	Комментарии к продукту
*}

{if $oProduct->getCommentsEnabled()}

	{*
		tip: параметр "bAllowNewComment" имеет неверное имя по логике действия в лс 1.0.3 - он запрещает добавление комментария, а не разрешает как можно понять по смыслу
	*}
	{include file='comment_tree.tpl'
		iTargetId=$oProduct->getId()
		sTargetType=PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT
		iAuthorId=$oProduct->getUser()->getId()
		iCountComment=$oProduct->getCommentCount()
		sDateReadLast=date('2030-01-01 00:00:00')
		aPagingCmt=$aPagingCmt
		bNoCommentFavourites=true

		bAllowSubscribe=true
		oSubscribeComment=$oProduct->getSubscribeNewComment()

		sAuthorNotice=$aLang.plugin.simplecatalog.Products.Item.comments.author
		bAllowNewComment=!$oProduct->getModerationDone()
		sNoticeNotAllow=$aLang.plugin.simplecatalog.Products.Item.comments.product_not_published
		sNoticeCommentAdd=$aLang.plugin.simplecatalog.Products.Item.comments.add_comment
	}

{elseif $oScheme->getAllowComments()==$SC_ALLOW_COMMENTS_USER_DEFINED}
	{*
		комментарии отключены автором продукта
	*}
	<div class="mb-20">
		{$aLang.plugin.simplecatalog.Products.Item.comments.disabled_by_author}
	</div>
{/if}
