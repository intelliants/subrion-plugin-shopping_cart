Ext.onReady(function()
{
	if (Ext.get('js-grid-placeholder'))
	{
		intelli.cart_items = new IntelliGrid(
		{
			columns: [
				'selection',
				{name: 'title', title: _t('title'), width: 1, sortable: false},
				{name: 'description', title: _t('description'), width: 2, sortable: false},
				{name: 'cost', title: _t('cost'), width: 70},
				'status',
				'update',
				'delete'
			],
			url: intelli.config.admin_url + '/shopping-cart/items/'
		}, false);

		intelli.cart_items.init();
	}
});