
	<dl class="fields-wrapper">
		<dt>
			<i class="sc-icon-question-sign js-tip-help fl-r"
			   title="{$aLang.plugin.simplecatalog.common.allowed_file_types_and_max_size|ls_lang:"types%%`$oField->getFileTypesAllowed()`":"size%%`$oField->getFileMaxSize()`"}"></i>
			{$oField->getTitle()}

			{$mValuePrefix = $oField->getValuePrefix()}
			{$mValuePostfix = $oField->getValuePostfix()}
			{if $mValuePrefix or $mValuePostfix}({$mValuePrefix}{if $mValuePrefix and $mValuePostfix}, {/if}{$mValuePostfix}){/if}

			{if $oField->getMandatoryEnabled()}
				<b>*</b>
			{/if}
		</dt>
		<dd>
			{*
				если файл загружен
			*}
			{if $sValue}
				<div class="mb-5 js-sc-one-uploaded-file-controls-wrapper">
					{$aLang.plugin.simplecatalog.Products.Add.fields.file.current}:
					{*
						если это редактирование - дать возможность удалить файл
					*}
					{if $_aRequest.id}
						<a href="#" class="js-sc-remove-product-field-file-value" title="{$aLang.plugin.simplecatalog.Delete}"
						   data-product-id="{$_aRequest.id}"
						   data-scheme-field-id="{$oField->getId()}"><i class="sc-icon-remove"></i></a>
					{/if}
					{*
						иконка загрузки файла
					*}
					<a href="{$sValue}" target="_blank" title="{$sValue|escape:'html'}"><i class="sc-icon-download"></i></a>
				</div>
			{/if}
			{*
				стилизированное поле для файла
			*}
			<div class="add-product file-field input-width-full">
				<div class="text-cover" data-default-html="{$aLang.plugin.simplecatalog.Products.Add.fields.file.select_file|escape:'html'}"
						>{$aLang.plugin.simplecatalog.Products.Add.fields.file.select_file}</div>
				<div class="input-wrapper">
					<input type="hidden" name="MAX_FILE_SIZE" value="{$oField->getFileMaxSize()*1024}" />
					<input type="file" name="{$sName}"
						   class="js-sc-file-field-check"
						   data-max-size="{$oField->getFileMaxSize()*1024}"
						   data-allowed-extensions="{$oField->getFileTypesAllowed()}"
						   size="50" />
				</div>
			</div>

			<span class="note">
				{$oField->getDescription()}
			</span>
		</dd>
	</dl>
