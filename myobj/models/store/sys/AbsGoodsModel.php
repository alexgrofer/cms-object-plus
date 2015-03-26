<?php

/**
 * Товары
 * Class GoodsModel
 */
abstract class AbsGoodsModel extends AbsBaseModel
{
	/**
	 * Название товара
	 * @var
	 */
	public $name;

	/**
	 * Возможные параметры:
	 * Артикль товара
	 * Описание товара
	 * Катогория товара
	 */

	abstract protected function getNameModelStore();
}
