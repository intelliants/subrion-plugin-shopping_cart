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

class iaBackendController extends iaAbstractControllerPluginBackend
{
	protected $_name = 'shopping-cart/categories';

	protected $_table = 'cart_categs';

	protected $_helperName = 'cartcategorie';

	protected $_pluginName = 'shopping_cart';

	protected $_phraseAddSuccess = 'cart_categ_added';
	protected $_phraseGridEntryDeleted = 'cart_categ_deleted';
	protected $_phraseGridEntriesDeleted = 'cart_categs_deleted';


	public function __construct()
	{
		parent::__construct();
		$this->_template = 'categories';

		$iaCart = $this->_iaCore->factoryPlugin($this->getPluginName(), iaCore::ADMIN, $this->_helperName);
		$this->setHelper($iaCart);
	}

	protected function _indexPage(&$iaView)
	{
		$iaView->grid('_IA_URL_plugins/' . $this->getPluginName() . '/js/admin/categories');
	}

	protected function _setPageTitle(&$iaView)
	{
		if (in_array($iaView->get('action'), array(iaCore::ACTION_ADD, iaCore::ACTION_EDIT)))
		{
			$iaView->title(iaLanguage::get('cart_categ_' . $iaView->get('action')));
		}
	}

	protected function _assignValues(&$iaView, array &$entryData)
	{
		$id = $this->getEntryId();
	
		$entryData['title'] = $this->_iaDb->keyvalue('`code`, `value`', "`key`='cart_categ_title_{$id}'", iaLanguage::getTable());
		$entryData['description'] = $this->_iaDb->keyvalue('`code`, `value`', "`key`='cart_categ_description_{$id}'", iaLanguage::getTable());
	}

	protected function _entryDelete($entryId)
	{
		return (bool)$this->getHelper()->delete($entryId);
	}

	protected function _preSaveEntry(array &$entry, array $data, $action)
	{
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
			iaLanguage::addPhrase('cart_categ_title_' . $id, $data['title'][$code], $code, $this->getPluginName());
			iaLanguage::addPhrase('cart_categ_description_' . $id, $data['description'][$code], $code, $this->getPluginName());
		}
	}

	protected function _gridQuery($columns, $where, $order, $start, $limit)
	{
		return parent::_gridQuery(array('id', 'order', 'image', 'status', 'update' => 1, 'delete' => 1), $where, $order, $start, $limit);
	}

	protected function _modifyGridResult(array &$entries)
	{
		$currentLanguage = $this->_iaCore->iaView->language;

		$this->_iaDb->setTable(iaLanguage::getTable());
		$titles = $this->_iaDb->keyvalue(array('key', 'value'), "`key` LIKE('cart_categ_title_%') && `code` = '$currentLanguage'");
		$descriptions = $this->_iaDb->keyvalue(array('key', 'value'), "`key` LIKE('cart_categ_description_%') && `code` = '$currentLanguage'");
		$this->_iaDb->resetTable();

		foreach ($entries as &$entry)
		{
			$entry['title'] = isset($titles["cart_categ_title_{$entry['id']}"]) ? $titles["cart_categ_title_{$entry['id']}"] : iaLanguage::get('empty');
			$entry['description'] = isset($descriptions["cart_categ_description_{$entry['id']}"]) ? $descriptions["cart_categ_description_{$entry['id']}"] : iaLanguage::get('empty');
		}
	}
}