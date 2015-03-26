<?php

/**
 * Склад
 * Class StoreModel
 */
abstract class AbsStoreModel extends AbsBaseModel
{
	/**
	 * Идентификатор товара
	 * @var
	 */
	public $goods_id;

	/**
	 * Колличество остатка товара
	 * колличество номеров класса single, или колличество билетов в партере концертного зала
	 * @var
	 */
	public $count;

	/**
	 * Возможные параметры:
	 * Идентификатор может быть номером места в кинотеатре или самолете, типом номера в отеле, или типом зоны на стадионе или концертном зале
	 * datetime отправления поезда или дата или дата заезда или время на которое естьв кинотеате места
	 * Колличество дней проживания в отеле
	 * id склада или магазина на котором есть товар
	 * id магазина продавца или авиаперевозчика или агенства недвижимости
	 */

	abstract protected function getNameModelGoods();

	public function relations() {
		return array(
			'goods'=>array(self::BELONGS_TO, $this->getNameModelGoods(), 'goods_id'),
		);
	}
}
