{if isset($citem)}
	<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
		{preventCsrf}
		<div class="wrap-list">
			<div class="wrap-group">
				<div class="wrap-group-heading">
					<h4>{lang key='options'}</h4>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label">{lang key='category'}</label>
					<div class="col col-lg-4">
						<select name="cid">
							<option value="">{lang key='_select_'}</option>
							{foreach from=$categs item=c}
								<option value="{$c}" {if isset($citem.cid) && $citem.cid == $c} selected="selected"{/if}>{lang key="cart_categ_title_"|cat:$c}</option>
							{/foreach}
						</select>
					</div>
				</div>

				{foreach $core.languages as $code => $language}
				<div class="row">
					<label class="col col-lg-2 control-label">{lang key='title'} <span class="label label-info">{$language.title}</span></label>

					<div class="col col-lg-4">
						<input type="text" name="title[{$code}]" value="{if isset($citem.title) && is_array($citem.title)}{if isset($citem.title.$code)}{$citem.title.$code|escape:'html'}{elseif isset($smarty.post.title.$code)}{$smarty.post.title.$code|escape:'html'}{/if}{/if}">
					</div>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label">{lang key='description'} <span class="label label-info">{$language.title}</span></label>

					<div class="col col-lg-8">
						<textarea name="description[{$code}]" rows="8" class="resizable">{if isset($citem.description) && is_array($citem.description)}{if isset($citem.description.$code)}{$citem.description.$code|escape:'html'}{elseif isset($smarty.post.description.$code)}{$smarty.post.description.$code|escape:'html'}{/if}{/if}</textarea>
					</div>
				</div>
				{/foreach}

				<div class="row">
					<label class="col col-lg-2 control-label" for="input-cost">{lang key='cost'}</label>
					<div class="col col-lg-4">
						<input type="text" name="cost" id="input-title" value="{if isset($citem.cost)}{$citem.cost}{elseif isset($smarty.post.cost)}{$smarty.post.cost}{/if}">
					</div>
				</div>

				{*
				<div class="row">
					<label class="col col-lg-2 control-label" for="input-days">{lang key='days'}</label>
					<div class="col col-lg-4">
						<input type="text" name="days" id="input-days" value="{if isset($citem.days)}{$citem.days}{elseif isset($smarty.post.days)}{$smarty.post.days}{/if}">
					</div>
				</div>
				*}

				<div class="row">
					<label class="col col-lg-2 control-label">{lang key='status'}</label>
					<div class="col col-lg-4">
						<select name="status">
							<option value="active" {if isset($citem.status) && iaCore::STATUS_ACTIVE == $citem.status}selected="selected"{/if}>{lang key='active'}</option>
							<option value="inactive" {if isset($citem.status) && iaCore::STATUS_INACTIVE == $citem.status}selected="selected"{/if}>{lang key='inactive'}</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-actions inline">
				<button type="submit" name="save" class="btn btn-primary">{if iaCore::ACTION_EDIT == $pageAction}{lang key='save_changes'}{else}{lang key='add'}{/if}</button>
			</div>
		</div>
	</form>
{else}
	{include file='grid.tpl'}
{/if}

{ia_print_js files='_IA_URL_plugins/shopping_cart/js/admin/items'}