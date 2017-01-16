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
 * @link https://subrion.org/
 *
 ******************************************************************************/

class iaBackendController extends iaAbstractControllerPluginBackend
{
	protected $_name = 'items';

	protected $_helperName = 'cart_item';

	protected $_pluginName = 'shopping_cart';

	protected $_gridColumns = array('id', 'order', 'cost', 'days', 'status', 'update' => 1, 'delete' => 1);

	protected $_phraseAddSuccess = 'cart_item_added';
	protected $_phraseGridEntryDeleted = 'cart_item_deleted';
	protected $_phraseGridEntriesDeleted = 'cart_items_deleted';


	public function init()
	{
		$this->_path = IA_ADMIN_URL . 'shopping-cart/' . $this->getName() . '/';
		$this->_template = 'items';

		$iaCartItem = $this->_iaCore->factoryPlugin($this->getPluginName(), iaCore::ADMIN, $this->_helperName);

		$this->setHelper($iaCartItem);
		$this->setTable($iaCartItem::getTable());
	}

	protected function _setPageTitle(&$iaView, array $entryData, $action)
	{
		if (in_array($iaView->get('action'), array(iaCore::ACTION_ADD, iaCore::ACTION_EDIT)))
		{
			$iaView->title(iaLanguage::get('cart_item_' . $iaView->get('action')));
		}
	}

	protected function _modifyGridResult(array &$entries)
	{
		$currentLanguage = $this->_iaCore->iaView->language;

		$this->_iaDb->setTable(iaLanguage::getTable());
		$titles = $this->_iaDb->keyvalue(array('key', 'value'), "`key` LIKE('cart_item_title_%') AND `code` = '$currentLanguage'");
		$descriptions = $this->_iaDb->keyvalue(array('key', 'value'), "`key` LIKE('cart_item_description_%') AND `code` = '$currentLanguage'");
		$this->_iaDb->resetTable();

		foreach ($entries as &$entry)
		{
			$entry['title'] = isset($titles["cart_item_title_{$entry['id']}"]) ? $titles["cart_item_title_{$entry['id']}"] : iaLanguage::get('empty');
			$entry['description'] = isset($descriptions["cart_item_description_{$entry['id']}"]) ? $descriptions["cart_item_description_{$entry['id']}"] : iaLanguage::get('empty');
		}
	}

	protected function _entryDelete($entryId)
	{
		return (bool)$this->getHelper()->delete($entryId);
	}

	protected function _preSaveEntry(array &$entry, array $data, $action)
	{
		if (empty($data['cid']))
		{
			$this->addMessage('cart_incorrect_categ');
		}
		$entry['cid'] = $data['cid'];

		iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

		$lang = array();
		$lang['title'] = $data['title'];
		$lang['description'] = $data['description'];

		foreach($this->_iaCore->languages as $code => $language)
		{
			if (empty($lang['title'][$code]))
			{
				$this->addMessage(iaLanguage::getf('error_lang_title', array('lang' => $language['title'])), false);
			}
			elseif (!utf8_is_valid($lang['title'][$code]))
			{
				$lang['title'][$code] = utf8_bad_replace($lang['title'][$code]);
			}

			if ($lang['description'][$code]  && !utf8_is_valid($lang['description'][$code]))
			{
				$lang['description'][$code] = utf8_bad_replace($lang['description'][$code]);
			}
		}

		if ($this->getMessages())
		{
			return false;
		}

		$entry['cost'] = isset($data['cost']) ? (float)$data['cost'] : '0.00';
		$entry['status'] = $data['status'];

		if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'])
		{
			$iaPicture = $this->_iaCore->factory('picture');

			$info = array(
				'image_width' => 1000,
				'image_height' => 750,
				'thumb_width' => 250,
				'thumb_height' => 250,
				'resize_mode' => iaPicture::CROP
			);

			if ($image = $iaPicture->processImage($_FILES['image'], iaUtil::getAccountDir(), iaUtil::generateToken(), $info))
			{
				empty($entry['image']) || $iaPicture->delete($entry['image']); // already has an assigned image
				$entry['image'] = $image;
			}
		}

		return true;
	}

	protected function _postSaveEntry(array &$entry, array $data, $action)
	{
		$id = $this->getEntryId();

		foreach ($this->_iaCore->languages as $code => $title)
		{
			iaLanguage::addPhrase('cart_item_title_' . $id, $data['title'][$code], $code, $this->getPluginName());
			iaLanguage::addPhrase('cart_item_description_' . $id, $data['description'][$code], $code, $this->getPluginName());
		}
	}

	protected function _assignValues(&$iaView, array &$entryData)
	{
		$categs = $this->_iaDb->onefield(iaDb::ID_COLUMN_SELECTION, '', 0, 0, 'cart_categs');
		$categs || $iaView->setMessages(iaLanguage::get('cart_error_no_categs'));

		$id = $this->getEntryId();

		$entryData['title'] = $this->_iaDb->keyvalue('`code`, `value`', "`key`='cart_item_title_{$id}'", iaLanguage::getTable());
		$entryData['description'] = $this->_iaDb->keyvalue('`code`, `value`', "`key`='cart_item_description_{$id}'", iaLanguage::getTable());

		$iaView->assign('categs', $categs);
	}
}