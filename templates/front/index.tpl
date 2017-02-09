{if !empty($all_items)}
	<form method="post" id="cart-form">
		<div class="cart">
			{foreach $all_items as $cid => $categ}
				<div class="cart-categ">
					<div class="cart-categ__heading clearfix">
						{if $categ.image}
							<div class="cart-categ__image">{ia_image file=$categ.image type='thumbnail' class='img-responsive'}</div>
						{/if}
						<h3>{lang key="cart_categ_title_{$cid}"}</h3>
						<p>{lang key="cart_categ_description_{$cid}"}</p>
					</div>

					<div class="cart-categ__content">
						{if !$categ.items}
							<div class="alert alert-info">No products in this category.</div>
						{else}
							<div class="cart-categ__items">
								<div class="row">
									{foreach $categ.items as $id => $item}
										{assign var='description' value="{lang key="cart_item_description_{$id}"}"}

										<div class="col-md-4">
											<div class="cart-categ__items__item">
												{if $item.image}
													<a href="#" data-toggle="modal" data-target="#modal_{$id}">
														{ia_image file=$item.image type='thumbnail' class='img-responsive'}
													</a>
												{/if}
												<h4>{lang key="cart_item_title_{$id}"}</h4>
												<p class="price"><span class="fa fa-tag"></span> {$item.cost} {$core.config.currency}</p>

												{if $core.config.shopping_cart_popup}
													<p>{$description|strip_tags|truncate:150:'...':true}</p>
												{else}
													<p>{$description}</p>
												{/if}

												<label class="cart-btn-buy">
													<input type="radio" class="js-cart-item" id="cart-item-{$id}" name="cart_items[{$cid}]" value="{$id}" data-cost="{$item.cost}" data-categ="{$cid}"><span>{lang key='buy_this_item'}</span>
												</label>

												{if $core.config.shopping_cart_popup}
													<button type="button" class="btn btn-primary cart-more-info" data-toggle="modal" data-target="#modal_{$id}">{lang key='more'}</button>

													<div class="modal fade" id="modal_{$id}" tabindex="-1" role="dialog">
														<div class="modal-dialog" role="document">
															<div class="modal-content">
																<div class="modal-body">
																	<div class="media">
																		{if $item.image}
																			<div class="media-left">
																				<a href="{ia_image file=$item.image url=true type='large'}" rel="ia_lightbox[{lang key="cart_item_title_{$id}"}]">{ia_image file=$item.image type='thumbnail' class='media-object' width=120}</a>
																			</div>
																		{/if}
																		<div class="media-body">
																			<h4 class="media-heading">{lang key="cart_item_title_{$id}"}</h4>
																			<p class="price"><span class="fa fa-tag"></span> {$item.cost} {$core.config.currency}</p>
																			<p>{$description}</p>
																		</div>
																	</div>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																</div>
															</div>
														</div>
													</div>
												{/if}
											</div>
										</div>

										{if $item@iteration % 3 == 0 && !$item@last}
											</div>
											<div class="row">
										{/if}
									{/foreach}
								</div>

								<div class="cart-categ__items__uncheck clearfix">
									<div class="radio">
										<label>
											<input type="radio" class="js-cart-item" name="cart_items[{$cid}]" id="cart-item-0" value="0" data-cost="0" data-categ="{$cid}" checked="checked"> {lang key='uncheck_items'}
										</label>
									</div>
								</div>
							</div>
						{/if}
					</div>
				</div>
			{/foreach}
		</div>
		<div class="cart-sum">
			<span>{lang key='total'}:</span>
			<span class="cart-sum__cost"><span id="cart-total">0</span> <span>{$core.config.currency}</span></span>
			<input type="hidden" name="total" id="total-cost">
			<button type="submit" class="btn btn-primary own-success btn-order" disabled>{lang key='checkout'}</button>
		</div>
	</form>

	{ia_add_media files='js:_IA_URL_plugins/shopping_cart/js/frontend/order, css:_IA_URL_plugins/shopping_cart/templates/front/css/style'}
{else}
	<div class="alert alert-info">{lang key='no_items'}</div>
{/if}