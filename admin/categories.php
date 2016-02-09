<?php
//##copyright##

$iaCartCategories = $iaCore->factoryPlugin(IA_CURRENT_PLUGIN, iaCore::ADMIN, 'cartcategories');
$iaDb->setTable(iaCartCategories::getTable());

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	switch ($pageAction)
	{
		case iaCore::ACTION_READ:
			$output = $iaCartCategories->gridRead($_GET,
				array('id', 'order'),
				array()
			);

			break;

		case iaCore::ACTION_DELETE:
			$output = $iaCartCategories->gridDelete($_POST);
	}

	$iaView->assign($output);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaView->title(iaLanguage::get('cart_categs'));

	$category = array();
	$categId = false;

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

			iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

			$lang = array();
			$lang['title'] = $_POST['title'];
			$lang['description'] = $_POST['description'];

			foreach($iaCore->languages as $code => $language)
			{
				if (isset($lang['title'][$code]))
				{
					if (empty($lang['title'][$code]))
					{
						$error = true;
						$messages[] = iaLanguage::getf('error_lang_title', array('lang' => $language['title']));
					}
					elseif (!utf8_is_valid($lang['title'][$code]))
					{
						$lang['title'][$code] = utf8_bad_replace($lang['title'][$code]);
					}
				}

				if (isset($lang['description'][$code]))
				{
					if (!utf8_is_valid($lang['description'][$code]))
					{
						$lang['description'][$code] = utf8_bad_replace($lang['description'][$code]);
					}
				}
			}

			if (!$error)
			{
				if (iaCore::ACTION_ADD == $pageAction)
				{
					$categId = $iaDb->insert(array('order' => 0));

					if ($categId)
					{
						$messages[] = iaLanguage::get('cart_categ_added');
					}
					else
					{
						$error = true;
						$messages[] = iaLanguage::get('db_error');
					}
				}
				elseif (iaCore::ACTION_EDIT == $pageAction)
				{
					$categId = $iaCore->requestPath[0];

					if ($categId)
					{
						$messages[] = iaLanguage::get('saved');
					}
					else
					{
						$error = true;
						$messages[] = iaLanguage::get('db_error');
					}
				}

				if (!$error)
				{
					foreach ($iaCore->languages as $code => $title)
					{
						iaLanguage::addPhrase('cart_categ_title_' . $categId, $lang['title'][$code], $code, IA_CURRENT_PLUGIN);
						iaLanguage::addPhrase('cart_categ_description_' . $categId, $lang['description'][$code], $code, IA_CURRENT_PLUGIN);
					}
				}

				$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
				iaUtil::go_to(IA_ADMIN_URL . 'shopping-cart/categories/');
			}

			$iaView->setMessages($messages);
		}

		if (iaCore::ACTION_EDIT == $pageAction)
		{
			$categId = empty($iaCore->requestPath[0]) ? false : (int)$iaCore->requestPath[0];
		}

		if ($categId)
		{
			$category['title'] = isset($categId)
				? $iaDb->keyvalue('`code`, `value`', "`key` = 'cart_categ_title_{$categId}'", iaLanguage::getTable())
				: false;
			$category['description'] = isset($categId)
				? $iaDb->keyvalue('`code`, `value`', "`key`='cart_categ_description_{$categId}'", iaLanguage::getTable())
				: false;
		}

		$iaView->assign('category', $category);
	}

	$iaView->display('categories');
}

$iaDb->resetTable();