{if iaCore::ACTION_ADD == $pageAction || iaCore::ACTION_EDIT == $pageAction}
	<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
		{preventCsrf}
		<div class="wrap-list">
			<div class="wrap-group">
				<div class="wrap-group-heading">
					<h4>{lang key='general'}</h4>
				</div>

				<div class="row">
					<ul class="nav nav-tabs">
						{foreach $core.languages as $code => $language}
							<li{if $language@iteration == 1} class="active"{/if}><a href="#tab-language-{$code}" data-toggle="tab" data-language="{$code}">{$language.title}</a></li>
						{/foreach}
					</ul>

					<div class="tab-content">
						{foreach $core.languages as $code => $language}
							<div class="tab-pane{if $language@first} active{/if}" id="tab-language-{$code}">
								<div class="row">
									<label class="col col-lg-2 control-label">{lang key='title'} {lang key='field_required'}</label>
									<div class="col col-lg-6">
										<input type="text" name="title[{$code}]" value="{if isset($category.title.$code)}{$category.title.$code|escape:'html'}{/if}">
									</div>
								</div>
								<div class="row js-local-url-field">
									<label class="col col-lg-2 control-label">{lang key='description'}</label>
									<div class="col col-lg-6">
										<textarea rows="8" class="resizable" name="description[{$code}]">{if isset($category.description.$code)}{$category.description.$code|escape:'html'}{/if}</textarea>
									</div>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
		<div class="form-actions inline">
			<input type="submit"  name="save" class="btn btn-primary" value="{lang key='save_changes'}">
		</div>
	</form>
{else}
	{include file='grid.tpl'}
{/if}

{ia_print_js files='_IA_URL_plugins/shopping_cart/js/admin/categories'}