<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2016 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaView->title(iaLanguage::get('order'));
	iaBreadcrumb::replaceEnd(iaLanguage::get('order'), IA_SELF);

	$all_items = $iaDb->assoc(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds(iaCore::STATUS_ACTIVE, 'status'), 'cart_categs');

	foreach ($all_items as $key => $categ)
	{
		$all_items[$key]['items'] = $iaDb->assoc(iaDb::ALL_COLUMNS_SELECTION, "`cid` = {$key} && `status` = 'active'", 'cart_items');
	}

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