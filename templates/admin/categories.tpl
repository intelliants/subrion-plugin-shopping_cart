{if iaCore::ACTION_ADD == $pageAction || iaCore::ACTION_EDIT == $pageAction}
	<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
		{preventCsrf}
		<div class="wrap-list">
			<div class="wrap-group">
				<div class="wrap-group-heading">
					<h4>{lang key='general'}</h4>
				</div>
				{foreach $core.languages as $code => $language}
					<div id="categories-data-{$code}" class="wrap-row">
						<div class="row">
							<label class="col col-lg-2 control-label">{lang key='title'} <span class="label label-info">{$language.title}</span></label>

							<div class="col col-lg-4">
								<input type="text" name="title[{$code}]" value="{if isset($category.title) && is_array($category.title)}{if isset($category.title.$code)}{$category.title.$code|escape:'html'}{elseif isset($smarty.post.title.$code)}{$smarty.post.title.$code|escape:'html'}{/if}{/if}">
							</div>
						</div>

						<div class="row">
							<label class="col col-lg-2 control-label">{lang key='description'} <span class="label label-info">{$language.title}</span></label>

							<div class="col col-lg-8">
								<textarea name="description[{$code}]" rows="8" class="resizable">{if isset($category.description) && is_array($category.description)}{if isset($category.description.$code)}{$category.description.$code|escape:'html'}{elseif isset($smarty.post.description.$code)}{$smarty.post.description.$code|escape:'html'}{/if}{/if}</textarea>
							</div>
						</div>
					</div>
				{/foreach}
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