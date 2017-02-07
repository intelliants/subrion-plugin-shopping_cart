{if iaCore::ACTION_ADD == $pageAction || iaCore::ACTION_EDIT == $pageAction}
	<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
		{preventCsrf}
		<div class="wrap-list">
			<div class="wrap-group">
				<div class="wrap-group-heading">
					<h4>{lang key='general'}</h4>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label" for="input-image">{lang key='image'}</label>
					<div class="col col-lg-4">
						{if !empty($item.image)}
							<div class="input-group thumbnail thumbnail-single with-actions">
								<a href="{ia_image file=$item.image type='large' url=true}" rel="ia_lightbox">
									{ia_image file=$item.image}
								</a>

								<div class="caption">
									<a class="btn btn-small btn-danger js-cmd-delete-file" href="#" title="{lang key='delete'}" data-file="{$item.image}" data-item="cart_categs" data-field="image" data-id="{$id}"><i class="i-remove-sign"></i></a>
								</div>
							</div>
						{/if}

						{ia_html_file name='image' id='input-image'}
					</div>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label">{lang key='status'}</label>
					<div class="col col-lg-4">
						<select name="status">
							<option value="active" {if isset($item.status) && iaCore::STATUS_ACTIVE == $item.status}selected="selected"{/if}>{lang key='active'}</option>
							<option value="inactive" {if isset($item.status) && iaCore::STATUS_INACTIVE == $item.status}selected="selected"{/if}>{lang key='inactive'}</option>
						</select>
					</div>
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
									<div class="col col-lg-10">
										<input type="text" name="title[{$code}]" value="{if isset($item.title.$code)}{$item.title.$code|escape:'html'}{/if}">
									</div>
								</div>
								<div class="row js-local-url-field">
									<label class="col col-lg-2 control-label">{lang key='description'}</label>
									<div class="col col-lg-10">
										<textarea rows="8" class="resizable" name="description[{$code}]">{if isset($item.description.$code)}{$item.description.$code|escape:'html'}{/if}</textarea>
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