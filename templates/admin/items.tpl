<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}
	<input type="hidden" name="language" id="js-active-language">

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="wrap-group-heading">
				<h4>{lang key='options'}</h4>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label">{lang key='category'} {lang key='field_required'}</label>
				<div class="col col-lg-4">
					<select name="cid">
						<option value="">{lang key='_select_'}</option>
						{foreach $categs as $category}
							<option value="{$category}" {if isset($item.cid) && $item.cid == $category} selected="selected"{/if} {if isset($smarty.post.cid) && $category == $smarty.post.cid}selected{/if}>{lang key="cart_categ_title_"|cat:$category}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-cost">{lang key='cost'}</label>
				<div class="col col-lg-4">
					<input type="text" name="cost" id="input-title" value="{if isset($item.cost)}{$item.cost}{elseif isset($smarty.post.cost)}{$smarty.post.cost}{/if}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-image">{lang key='image'}</label>
				<div class="col col-lg-4">
					{if isset($item.image) && $item.image}
						<div class="input-group thumbnail thumbnail-single with-actions">
							<a href="{printImage imgfile=$item.image fullimage=true url=true}" rel="ia_lightbox">
								{printImage imgfile=$item.image}
							</a>

							<div class="caption">
								<a class="btn btn-small btn-danger" href="javascript:void(0);" title="{lang key='delete'}" onclick="return intelli.admin.removeFile('{$item.image}',this,'blog_entries','image','{$id}')"><i class="i-remove-sign"></i></a>
							</div>
						</div>
					{/if}

					{ia_html_file name='image' id='input-image'}
				</div>
			</div>

			{*
			<div class="row">
				<label class="col col-lg-2 control-label" for="input-days">{lang key='days'}</label>
				<div class="col col-lg-4">
					<input type="text" name="days" id="input-days" value="{if isset($item.days)}{$item.days}{elseif isset($smarty.post.days)}{$smarty.post.days}{/if}">
				</div>
			</div>
			*}

			<div class="row">
				<label class="col col-lg-2 control-label">{lang key='status'}</label>
				<div class="col col-lg-4">
					<select name="status">
						<option value="active" {if isset($item.status) && iaCore::STATUS_ACTIVE == $item.status}selected="selected"{/if}>{lang key='active'}</option>
						<option value="inactive" {if isset($item.status) && iaCore::STATUS_INACTIVE == $item.status}selected="selected"{/if}>{lang key='inactive'}</option>
					</select>
				</div>
			</div>

			<div class="wrap-group" id="js-content-fields">
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
										<textarea rows="30" name="description[{$code}]">{if isset($item.description.$code)}{$item.description.$code|escape:'html'}{/if}</textarea>
									</div>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>

		<div class="form-actions inline">
			<button type="submit" name="save" class="btn btn-primary">{if iaCore::ACTION_EDIT == $pageAction}{lang key='save_changes'}{else}{lang key='add'}{/if}</button>
		</div>
	</div>
</form>

{ia_print_js files='ckeditor/ckeditor, _IA_URL_plugins/shopping_cart/js/admin/items'}