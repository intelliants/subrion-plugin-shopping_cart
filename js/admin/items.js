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

$(function()
{
	// Page content language tabs
	$('a[data-toggle="tab"]', '#js-content-fields').on('shown.bs.tab', function()
	{
		var lngCode = $(this).data('language');
		CKEDITOR.instances['description[' + lngCode + ']']
			|| intelli.ckeditor('description[' + lngCode + ']', {toolbar: 'Extended'});

		$('#js-active-language').val(lngCode);
	});
	$('a[data-toggle="tab"]:first', '#js-content-fields').trigger('shown.bs.tab');
});