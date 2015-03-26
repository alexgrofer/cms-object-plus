<?php

/**
 * Цены
 * Class StoreModel
 */
abstract class AbsPriceModel extends AbsBaseModel
{
	/**
	 * Идентификатор товара
	 * @var
	 */
	public $goods_id;

	/**
	 * @var Цена товара
	 */
	public $total;

	/**
	 * Остальные идентификаторы типа города, страны, или ценовой колонке клиента
	 */
}
