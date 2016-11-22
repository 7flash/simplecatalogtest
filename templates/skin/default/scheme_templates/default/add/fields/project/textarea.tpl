
<!--script src='//cdn.tinymce.com/4/tinymce.min.js'></script>
<script>
	tinymce.init({
		selector: '#js_id__field_{$oField->getCode()}'
	});

</script-->


<!--script src="//cdn.ckeditor.com/4.5.10/standard/ckeditor.js"></script-->

<!-- https://retina.news.mail.ru/pic/0e/5f/image26856793_6743154489cae842839f07bb95c70ed0.jpg -->

<script>
	//var CKEDITOR_BASEPATH = '/engine/lib/external/ckeditor/';

	$(function(){

		/*
		CKEDITOR.replace('{$sName}',  {
			customConfig : ''
		});
		*/

		/*
		var editor_detailtext = CKEDITOR.replace(
				new CKEDITOR.dom.element( document.getElementById( 'js_id_field_detailtext' ) )

				,  {
			customConfig : ''
		});


		CKEDITOR.config.language = 'ru';
		CKEDITOR.config.toolbar = [
			{ name: 'items', items: [ "Source"] },

			{ name: 'insert', items: ['Image', 'Youtube']},

			{ name: 'items11', items: ["UIColor",        "Maximize",        "ShowBlocks"  ] }

		];

		CKEDITOR.config.extraPlugins = 'youtube';

		CKEDITOR.plugins.addExternal( 'youtube', '/engine/lib/external/ckeditor/plugins/youtube/', 'plugin.js' );
		*/
	});
</script>
<!--script src="//cdn.ckeditor.com/4.5.10/standard/ckeditor.js"></script-->


<div class="form-group">
	<label class="col-md-4 control-label">

		{$oField->getTitle()}

		{$mValuePrefix = $oField->getValuePrefix()}
		{$mValuePostfix = $oField->getValuePostfix()}
		{if $mValuePrefix or $mValuePostfix}({$mValuePrefix}{if $mValuePrefix and $mValuePostfix}, {/if}{$mValuePostfix}){/if}

		{if $oField->getMandatoryEnabled()}
			<span class="color_red fwb">*</span>
		{/if}

	</label>

	<div class="cb"></div>
	<div class="control-field col-md-13" style="padding-left: 15px;">
		<!-- class - multi-line-editor mce-editor markitup-editor -->
		<textarea id="js_id_field_{$oField->getCode()}" name="{$sName}" class="js_c_field_{$oField->getCode()} form-control " maxlength="{$oField->getTextareaMaxLength()}">{$sValue}</textarea>

		<span class="note">{$oField->getDescription()}</span>

		<div class="js_c_err_{$oField->getCode()} js_c_sub_field_err"></div>
	</div>

</div>

