<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaView->title(iaLanguage::get('order'));
	iaBreadcrumb::replaceEnd(iaLanguage::get('order'), IA_SELF);

	$iaDb->setTable('cart_categs');
	$all_items = $iaDb->assoc();
	$iaDb->resetTable();

	$iaDb->setTable('cart_items');
	foreach ($all_items as $key => $categ)
	{
		$all_items[$key]['items'] = $iaDb->assoc(iaDb::ALL_COLUMNS_SELECTION, "`cid` = {$key}");
	}
	$iaDb->resetTable();

	$iaTransaction = $iaCore->factory('transaction', iaCore::CORE);

	if ($_POST)
	{
		$gateways = $iaTransaction->getPaymentGateways();

		$transactionId = 0;
		if(isset($_POST['transaction_id']))
		{
			$transactionId = iaSanitize::sql($_POST['transaction_id']);
		}

		$iaView->title(iaLanguage::get('products_in_cart'));
		iaBreadcrumb::add(iaLanguage::get('order'), IA_URL . 'order/');
		iaBreadcrumb::replaceEnd(iaLanguage::get('products_in_cart'), IA_SELF);
		$iaView->assign('checkout', 1);

		$selected_products = array();
		$title = array();

		foreach ($_POST['cart_items'] as $categ => $product)
		{
			if ($product != '0')
			{
				$selected_products[$product] = $all_items[$categ]['items'][$product];
				$title[] = iaLanguage::get('cart_item_title_' . $product) . ' - ' . iaLanguage::get('cart_categ_title_' . $categ);
			}
		}

		$title = implode(', ', $title);

		$paymentId = $iaTransaction->create($title, $_POST['total'], 'cart_purchase', array(), IA_URL . 'order/', 0, true);

		iaUtil::go_to(IA_URL . 'pay' . IA_URL_DELIMITER . $paymentId . IA_URL_DELIMITER);
	}
	else
	{
		$iaView->assign('all_items', $all_items);
	}

	$iaView->display('index');
}