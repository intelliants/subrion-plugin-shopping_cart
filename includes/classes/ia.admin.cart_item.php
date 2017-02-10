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

class iaCartItem extends abstractPlugin
{
	protected static $_table = 'cart_items';

	public $dashboardStatistics = true;


	public function getDashboardStatistics()
	{
		$statuses = array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE);
		$rows = $this->iaDb->keyvalue('`status`, COUNT(*)', '1 GROUP BY `status`', self::getTable());
		$total = 0;

		foreach ($statuses as $status)
		{
			isset($rows[$status]) || $rows[$status] = 0;
			$total+= $rows[$status];
		}

		return array(
			'icon' => 'tags',
			'item' => iaLanguage::get('products'),
			'caption' => iaLanguage::get('shopping_cart'),
			'rows' => $rows,
			'total' => $total,
			'url' => 'shopping-cart/items/'
		);
	}

	public function delete($id)
	{
		$result = false;

		$this->iaDb->setTable(self::getTable());

		// if item exists, then remove it
		if ($row = $this->iaDb->row_bind(array('image'), '`id` = :id', array('id' => $id)))
		{
			$result = (bool)$this->iaDb->delete(iaDb::convertIds($id), self::getTable());

			if ($row['image'] && $result) // we have to remove the assigned image as well
			{
				$iaPicture = $this->iaCore->factory('picture');
				$iaPicture->delete($row['image']);
			}

			if ($result)
			{
				iaLanguage::delete('cart_item_title_' . $id);
				iaLanguage::delete('cart_item_description_' . $id);

				$this->iaCore->factory('log')->write(iaLog::ACTION_DELETE, array(
					'module' => 'blog',
					'item' => 'product',
					'name' => $row['title'],
					'id' => (int)$id
				));
			}
		}

		$this->iaDb->resetTable();

		return $result;
	}
}