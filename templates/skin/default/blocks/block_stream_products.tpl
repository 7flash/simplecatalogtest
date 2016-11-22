
<!-- Simplecatalog plugin -->
<ul class="latest-list">
	{foreach $aComments as $oComment}
		{assign var="oUser" value=$oComment->getUser()}
		{assign var="oProduct" value=$oComment->getProduct()}

		<li class="js-title-comment" title="{$oComment->getText()|strip_tags|trim|truncate:100:'...'|escape:'html'}">
			<p>
				<a href="{$oUser->getUserWebPath()}" class="author">{$oUser->getLogin()}</a>
				<time datetime="{date_format date=$oComment->getDate() format='c'}" title="{date_format date=$oComment->getDate() format="j F Y, H:i"}">
					{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
				</time>
			</p>
			<a href="{if Config::Get('module.comment.nested_per_page')}{router page='comments'}{else}{$oProduct->getItemShowWebPath()}#comment{/if}{$oComment->getId()}"
			   class="stream-product">{$oProduct->getFirstFieldTitle()}</a>
			<span class="block-item-comments"><i class="sc-icon-synio-comments-small"></i>{$oProduct->getCommentCount()}</span>
		</li>
	{/foreach}
</ul>
<!-- /Simplecatalog plugin -->
