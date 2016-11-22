<!doctype html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>{$sHtmlTitle}</title>
	<meta name="description" content="{$sHtmlDescription}">
	<meta name="keywords" content="{$sHtmlKeywords}">

	{$aHtmlHeadFiles.css}

	<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

	<script>
		var DIR_WEB_ROOT 			= '{cfg name="path.root.web"}';
		var DIR_STATIC_SKIN 		= '{cfg name="path.static.skin"}';
		var DIR_ROOT_ENGINE_LIB 	= '{cfg name="path.root.engine_lib"}';
		var LIVESTREET_SECURITY_KEY = '{$LIVESTREET_SECURITY_KEY}';
		var SESSION_ID				= '{$_sPhpSessionId}';
		var BLOG_USE_TINYMCE		= '{cfg name="view.tinymce"}';

		var TINYMCE_LANG = 'en';
		{if $oConfig->GetValue('lang.current') == 'russian'}
			TINYMCE_LANG = 'ru';
		{/if}

		var aRouter = new Array();
		{foreach from=$aRouter key=sPage item=sPath}
			aRouter['{$sPage}'] = '{$sPath}';
		{/foreach}
	</script>

	{$aHtmlHeadFiles.js}
</head>
<body>

	{include file='system_message.tpl'}

	<div class="Simplecatalog Embed Product Item">
		{sc_scheme_template scheme=$oScheme file="embed.tpl"}
	</div>

</body>
</html>