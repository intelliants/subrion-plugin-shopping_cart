<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
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

class iaBackendController extends iaAbstractControllerModuleBackend
{
	protected $_name = 'categories';

	protected $_table = 'cart_categs';

	protected $_moduleName = 'shopping_cart';

	protected $_gridColumns = ['id', 'order', 'image', 'status'];

	protected $_phraseAddSuccess = 'cart_categ_added';
	protected $_phraseGridEntryDeleted = 'cart_categ_deleted';
	protected $_phraseGridEntriesDeleted = 'cart_categs_deleted';


	public function init()
	{
		$this->_path = IA_ADMIN_URL . 'shopping-cart/' . $this->getName() . '/';
		$this->_template = 'categories';
	}

	protected function _setPageTitle(&$iaView, array $entryData, $action)
	{
		if (in_array($action, [iaCore::ACTION_ADD, iaCore::ACTION_EDIT]))
		{
			$iaView->title(iaLanguage::get('cart_categ_' . $iaView->get('action')));
		}
	}

	protected function _modifyGridResult(array &$entries)
	{
		$currentLanguage = $this->_iaCore->iaView->language;

		$this->_iaDb->setTable(iaLanguage::getTable());
		$titles = $this->_iaDb->keyvalue(['key', 'value'], "`key` LIKE('cart_categ_title_%') && `code` = '$currentLanguage'");
		$descriptions = $this->_iaDb->keyvalue(['key', 'value'], "`key` LIKE('cart_categ_description_%') && `code` = '$currentLanguage'");
		$this->_iaDb->resetTable();

		foreach ($entries as &$entry)
		{
			$entry['title'] = isset($titles["cart_categ_title_{$entry['id']}"]) ? $titles["cart_categ_title_{$entry['id']}"] : iaLanguage::get('empty');
			$entry['description'] = isset($descriptions["cart_categ_description_{$entry['id']}"]) ? $descriptions["cart_categ_description_{$entry['id']}"] : iaLanguage::get('empty');
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
		$row = $this->getById($entryId);
		$result = (bool)$this->getHelper()->delete($entryId);

		if ($result && $row)
		{
			// we have to remove the assigned image as well
			empty($row['image']) || $this->_iaCore->factory('picture')->delete($row['image']);

			//$this->iaCore->factory('log')->write(iaLog::ACTION_DELETE, array('module' => 'blog', 'item' => 'blog', 'name' => $row['title'], 'id' => (int)$id));
		}

		return $result;
	}

	protected function _preSaveEntry(array &$entry, array $data, $action)
	{
		iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

		$lang = [];
		$lang['title'] = $data['title'];
		$lang['description'] = $data['description'];

		foreach($this->_iaCore->languages as $code => $language)
		{
			if (empty($lang['title'][$code]))
			{
				$this->addMessage(iaLanguage::getf('error_lang_title', ['lang' => $language['title']]), false);
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

		if (isset($_FILES['image']['error']) && !$_FILES['image']['error'])
		{
			try
			{
				$iaField = $this->_iaCore->factory('field');

				$path = $iaField->uploadImage($_FILES['image'], 1000, 750, 250, 250, 'crop');

				empty($entry['image']) || $iaField->deleteUploadedFile('image', $this->getTable(), $this->getEntryId(), $entry['image']);
				$entry['image'] = $path;
			}
			catch (Exception $e)
			{
				$this->addMessage($e->getMessage(), false);
			}
		}

		return !$this->getMessages();
	}

	protected function _postSaveEntry(array &$entry, array $data, $action)
	{
		$id = $this->getEntryId();

		foreach ($this->_iaCore->languages as $code => $title)
		{
			iaLanguage::addPhrase('cart_categ_title_' . $id, $data['title'][$code], $code, $this->getModuleName());
			iaLanguage::addPhrase('cart_categ_description_' . $id, $data['description'][$code], $code, $this->getModuleName());
		}
	}
}