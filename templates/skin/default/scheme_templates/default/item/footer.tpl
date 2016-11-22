
{*
	Футер продукта
*}

<ul class="product-footer">
	{*
		автор
	*}
	<li class="author" title="{$aLang.plugin.simplecatalog.common.author}" itemprop="author" itemscope itemtype="http://schema.org/Person">
		<a href="{$oProduct->getUser()->getUserWebPath()}" itemprop="url"><img src="{$oProduct->getUser()->getProfileAvatarPath(24)}" alt="" class="avatar" itemprop="image" /></a>
		<a href="{$oProduct->getUser()->getUserWebPath()}" itemprop="name">{$oProduct->getUser()->getLogin()}</a>
	</li>
	<li class="arrow">&rarr;</li>
	{*
		дата публикации
	*}
	<li class="date-add">
		<time datetime="{date_format date=$oProduct->getAddDate() format='c'}"
			  title="{$aLang.plugin.simplecatalog.common.date_add}: {date_format date=$oProduct->getAddDate() format='j F Y, H:i'}" itemprop="dateCreated datePublished">
			{date_format date=$oProduct->getAddDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
		</time>
	</li>

	&nbsp;

	{*
		последний редактировал
	*}
	<li class="editor-last" title="{$aLang.plugin.simplecatalog.common.editor_last}" itemprop="editor" itemscope itemtype="http://schema.org/Person">
		<a href="{$oProduct->getUserEditLast()->getUserWebPath()}" itemprop="url"><img src="{$oProduct->getUserEditLast()->getProfileAvatarPath(24)}" alt="" class="avatar" itemprop="image" /></a>
		<a href="{$oProduct->getUserEditLast()->getUserWebPath()}" itemprop="name">{$oProduct->getUserEditLast()->getLogin()}</a>
	</li>
	<li class="arrow">&rarr;</li>
	{*
		дата последнего редактирования
	*}
	<li class="date-edit-last">
		<time datetime="{date_format date=$oProduct->getEditDate() format='c'}"
			  title="{$aLang.plugin.simplecatalog.common.date_edit_last}:  {date_format date=$oProduct->getEditDate() format='j F Y, H:i'}" itemprop="dateModified">
			{date_format date=$oProduct->getEditDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
		</time>
	</li>

	{*
		количество комментариев
	*}
	{if $bProductList and $oProduct->getCommentsEnabled()}
		<li class="product-comments" title="{$aLang.plugin.simplecatalog.common.comments_count}">
			<a href="{$oProduct->getItemShowWebPath()}#comments" itemprop="discussionUrl"><i class="sc-icon-comment"></i>{$oProduct->getCommentCount()}</a>
		</li>
		<meta itemprop="interactionCount" content="UserComments:{$oProduct->getCommentCount()}" style="display: none" />
	{/if}

	{*
		количество меток на карте
	*}
	{if $bProductList and $oScheme->getMapItemsEnabled()}
		{$iProductMapItemsCount = count($oProduct->getMapItems())}
		{if $iProductMapItemsCount}
			<li title="{$aLang.plugin.simplecatalog.common.map_items_count}">
				<a href="{$oProduct->getItemShowWebPath()}#product_map"><i class="sc-icon-map-marker"></i>{$iProductMapItemsCount}</a>
			</li>
		{/if}
	{/if}

	{*
		количество просмотров продукта
	*}
	{if $iViewsCount = $oProduct->getViewsCount()}
		<li title="{$aLang.plugin.simplecatalog.common.views_count}">
			<i class="sc-icon-eye-open"></i><span>{$iViewsCount}</span>
		</li>
	{/if}

	{hook run='sc_product_item_footer' oProduct=$oProduct oScheme=$oScheme bProductList=$bProductList}
</ul>
