<?php
//##copyright##

class iaCartItems extends abstractPlugin
{
	protected static $_table = 'cart_items';

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
				$data[$key]['title'] = iaLanguage::get('cart_item_title_' . $row['id']);
				$data[$key]['description'] = iaLanguage::get('cart_item_description_' . $row['id']);
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