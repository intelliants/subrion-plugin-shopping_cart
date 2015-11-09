$(function() {
	var selected_categs = new Object;

	$('.js-cart-item').click(function() 
	{
		var total = parseFloat($('#cart-total').text());
		var selected = parseFloat($(this).attr('data-cost'));
		var categ = $(this).attr('data-categ');
		var result = 0;

		selected_categs[categ] = selected;

		$.each(selected_categs, function(i, v){
			result += v;
		});

		if (result > 0)
		{
			$('.own-success').prop('disabled', false);
		}
		else {
			$('.own-success').prop('disabled', true);
		}

		$('#cart-total').text(result);
		$('#total-cost').val(result);
	});
});