Ext.onReady(function()
{
	if (Ext.get('js-grid-placeholder'))
	{
		new IntelliGrid(
		{
			columns:[
				'selection',
				{name: 'title', title: _t('title'), width: 200},
				{name: 'description', title: _t('description'), width: 1},
				'status',
				'update',
				'delete'
			],
			texts: {
				delete_multiple: _t('cart_are_you_sure_to_delete_selected_categs'),
				delete_single: _t('cart_are_you_sure_to_delete_selected_categ')
			}
		});
	}
});