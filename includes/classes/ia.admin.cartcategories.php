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

class iaCartCategories extends abstractPlugin
{
	protected static $_table = 'cart_categs';

	public function delete($id)
	{
		$result = false;

		$this->iaDb->setTable(self::getTable());

		// if item exists, then remove it
		if ($row = $this->iaDb->row_bind(array('title', 'image'), '`id` = :id', array('id' => $id)))
		{
			$result = (bool)$this->iaDb->delete(iaDb::convertIds($id), self::getTable());

			if ($row['image'] && $result) // we have to remove the assigned image as well
			{
				$iaPicture = $this->iaCore->factory('picture');
				$iaPicture->delete($row['image']);
			}

			if ($result)
			{
				$this->iaCore->factory('log')->write(iaLog::ACTION_DELETE, array('module' => 'blog', 'item' => 'blog', 'name' => $row['title'], 'id' => (int)$id));
			}
		}

		$this->iaDb->resetTable();

		return $result;
	}

	public function gridRead($params, $columns, array $filterParams = array(), array $persistentConditions = array())
	{
		$params || $params = array();

		$start = isset($params['start']) ? (int)$params['start'] : 0;
		$limit = isset($params['limit']) ? (int)$params['limit'] : 15;

		$sort = $params['sort'];
		$dir = in_array($params['dir'], array(iaDb::ORDER_ASC, iaDb::ORDER_DESC)) ? $params['dir'] : iaDb::ORDER_ASC;
		$order = ($sort && $dir) ? " ORDER BY `{$sort}` {$dir}" : '';

		$where = $values = array();
		foreach ($filterParams as $name => $type)
		{
			if (isset($params[$name]) && $params[$name])
			{
				$value = iaSanitize::sql($params[$name]);

				switch ($type)
				{
					case 'equal':
						$where[] = sprintf('`%s` = :%s', $name, $name);
						$values[$name] = $value;
						break;
					case 'like':
						$where[] = sprintf('`%s` LIKE :%s', $name, $name);
						$values[$name] = '%' . $value . '%';
				}
			}
		}

		$where = array_merge($where, $persistentConditions);
		$where || $where[] = iaDb::EMPTY_CONDITION;
		$where = implode(' AND ', $where);
		$this->iaDb->bind($where, $values);

		if (is_array($columns))
		{
			$columns = array_merge(array('id', 'update' => 1, 'delete' => 1), $columns);
		}

		$data = $this->iaDb->all($columns, $where . $order, $start, $limit);

		if ($data)
		{
			foreach ($data as $key => $row)
			{
				$data[$key]['title'] = iaLanguage::get('cart_categ_title_' . $row['id']);
				$data[$key]['description'] = iaLanguage::get('cart_categ_description_' . $row['id']);
			}
		}

		return array(
			'data' => $data,
			'total' => (int)$this->iaDb->one(iaDb::STMT_COUNT_ROWS, $where)
		);
	}

	public function gridDelete($params, $languagePhraseKey = 'deleted')
	{
		$result = array(
			'result' => false,
			'message' => iaLanguage::get('invalid_parameters')
		);

		if (isset($params['id']) && is_array($params['id']) && $params['id'])
		{
			$total = count($params['id']);
			$affected = $this->iaDb->delete('`id` IN (' . implode(',', $params['id']) . ')');

			if (1 == $total)
			{
				$result['result'] = (1 == $affected);
				$result['message'] = $result['result']
					? iaLanguage::get($languagePhraseKey)
					: iaLanguage::get('db_error');
			}
			else
			{
				$result['result'] = ($affected == $total);
				$result['message'] = $result['result']
					? iaLanguage::getf('items_deleted', array('num' => $affected))
					: iaLanguage::getf('items_deleted_of', array('num' => $affected, 'total' => $total));
			}
		}

		return $result;
	}
}