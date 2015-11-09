Ext.onReady(function()
{
	if (Ext.get('js-grid-placeholder'))
	{
		intelli.cart_items = new IntelliGrid(
		{
			columns: [
				'selection',
				{name: 'title', title: _t('title'), width: 1},
				{name: 'description', title: _t('description'), width: 2},
				{name: 'cost', title: _t('cost'), width: 70},
				'status',
				'update',
				'delete'
			],
			url: intelli.config.admin_url + '/shopping-cart/items/',
			texts: {
				delete_multiple: _t('are_you_sure_to_delete_selected_categs'),
				delete_single: _t('are_you_sure_to_delete_selected_categ')
			}
		}, false);

		intelli.cart_items.init();
	}
});