<?php
//##copyright##

$iaCartItems = $iaCore->factoryPlugin(IA_CURRENT_PLUGIN, iaCore::ADMIN, 'cartitems');
$iaDb->setTable(iaCartItems::getTable());

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	switch ($pageAction)
	{
		case iaCore::ACTION_READ:
			$params = array();

			$output = $iaCartItems->gridRead($_GET,
				array('id', 'order', 'cost', 'days', 'status'),
				array('status' => 'equal'),
				$params
			);

		break;

		case iaCore::ACTION_EDIT:
			$output = $iaCartItems->gridUpdate($_POST);

		break;

		case iaCore::ACTION_DELETE:
			$output = $iaCartItems->gridDelete($_POST);
	}

	$iaView->assign($output);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaView->title(iaLanguage::get('cart_items'));

	$categs = $iaDb->onefield('id', '', 0, 0, 'cart_categs');

	if (iaCore::ACTION_READ == $pageAction)
	{
		$iaView->grid();
	}
	else
	{
		if (isset($_POST['save']))
		{
			$error = false;
			$messages = array();
			$data = array();

			if (empty($_POST['cid']))
			{
				$error = true;
				$messages[] = iaLanguage::get('cart_incorrect_categ');
			}
			else
			{
				$data['cid'] = in_array($_POST['cid'], $categs) ? $_POST['cid'] : false;

				iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

				$lang = array();
				$lang['title'] = $_POST['title'];
				$lang['description'] = $_POST['description'];

				foreach($iaCore->languages as $citem_language => $citem_language_title)
				{
					if (isset($lang['title'][$citem_language]))
					{
						if (empty($lang['title'][$citem_language]))
						{
							$error = true;
							$messages[] = iaLanguage::getf('error_lang_title', array('lang' => $citem_language_title));
						}
						elseif (!utf8_is_valid($lang['title'][$citem_language]))
						{
							$lang['title'][$citem_language] = utf8_bad_replace($lang['title'][$citem_language]);
						}
					}

					if (isset($lang['description'][$citem_language]))
					{
						if (!utf8_is_valid($lang['description'][$citem_language]))
						{
							$lang['description'][$citem_language] = utf8_bad_replace($lang['description'][$citem_language]);
						}
					}
				}

				$data['cost'] = (isset($_POST['cost']) && !empty($_POST['cost'])) ? $_POST['cost'] : '0';
				if (!preg_match('/^[0-9\.]+$/', $data['cost']))
				{
					$error = true;
					$messages[] = iaLanguage::get('error_cart_item_cost');
				}
				$data['status'] = $_POST['status'];

				if (!$error)
				{
					if (isset($data['data']) && is_array($data['data']))
					{
						$data['data'] = serialize($data['data']);
					}

					if (iaCore::ACTION_ADD == $pageAction)
					{
						$citem_id = $iaDb->insert($data);

						if ($citem_id)
						{
							$messages[] = iaLanguage::get('cart_item_added');
						}
						else
						{
							$error = true;
							$messages[] = iaLanguage::get('unknown_error');
						}
					}
					elseif (iaCore::ACTION_EDIT == $pageAction)
					{
						$citem_id = $iaCore->requestPath[0];
						$result = $iaDb->update($data, iaDb::convertIds($citem_id));

						if ($result !== false)
						{
							$messages[] = iaLanguage::get('saved');
						}
						else
						{
							$error = true;
							$messages[] = iaLanguage::get('unknown_error');
						}
					}

					foreach ($iaCore->languages as $code => $title)
					{
						iaLanguage::addPhrase('cart_item_title_' . $citem_id, $lang['title'][$code], $code, IA_CURRENT_PLUGIN);
						iaLanguage::addPhrase('cart_item_description_' . $citem_id, $lang['description'][$code], $code, IA_CURRENT_PLUGIN);
					}

					$iaView->setMessages($messages, ($error ? 'error' : 'success'));
					iaUtil::go_to(IA_ADMIN_URL . 'shopping-cart/items/');
				}
			}

			$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
		}

		if (iaCore::ACTION_EDIT == $pageAction)
		{
			$citem_id = empty($iaCore->requestPath[0]) ? false : (int)$iaCore->requestPath[0];
			$citem = $citem_id ? $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, "`id`='{$citem_id}'", 'cart_items') : false;
		}

		$titles = $description = '';

		if (isset($citem_id) && $citem_id)
		{
			$citem['title'] = isset($citem_id)
				? $iaDb->keyvalue('`code`, `value`', "`key`='cart_item_title_{$citem_id}'", 'language')
				: false;
			$citem['description'] = isset($citem_id)
				? $iaDb->keyvalue('`code`, `value`', "`key`='cart_item_description_{$citem_id}'", 'language')
				: false;
		}

		if (isset($citem['data']))
		{
			$citem['data'] = unserialize($citem['data']);
		}
		else
		{
			$citem['data'] = array();
		}

		$iaView->assign('citem', $citem);
	}

	$iaView->assign('categs', $categs);

	$iaView->display('items');
}

$iaView->name('cart_items');
$iaDb->resetTable();