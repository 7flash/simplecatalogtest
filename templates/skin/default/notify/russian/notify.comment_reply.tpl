Пользователь <a href="{$oUserComment->getUserWebPath()}">{$oUserComment->getLogin()}</a> ответил на ваш комментарий в <b>«{$oProduct->getFirstFieldTitle()}»</b>,
прочитать его можно перейдя по
<a href="{if Config::Get('module.comment.nested_per_page')}{router page='comments'}{else}{$oProduct->getItemShowWebPath()}#comment{/if}{$oComment->getId()}">этой ссылке</a>
<br>
{if Config::Get('sys.mail.include_comment')}
	Текст сообщения: <i>{$oComment->getText()}</i>
{/if}

<br/><br/>
<a href="{Config::Get('path.root.web')}">{Config::Get('view.name')}</a>
