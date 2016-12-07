Ext.onReady(function()
{
	var pageUrl = intelli.config.admin_url + '/shopping-cart/categories/';

	if (Ext.get('js-grid-placeholder'))
	{
		intelli.cart_categs = new IntelliGrid({
			columns:[
				'selection',
				{name: 'title', title: _t('title'), width: 200},
				{name: 'description', title: _t('description'), width: 1},
				'status',
				'update',
				'delete'
			],
			url: pageUrl,
			texts: {
				delete_multiple: _t('cart_are_you_sure_to_delete_selected_categs'),
				delete_single: _t('cart_are_you_sure_to_delete_selected_categ')
			}
		}, false);

		intelli.cart_categs.init();
	}
});