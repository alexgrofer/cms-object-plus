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
	 *
	 * доп опции: шарина высота
	 */

	/**
	 * ссылка на католог, какой категории пренадлежит этот товар
	 * @var
	 */
	public $catalog_id;

	abstract protected function getNameModelStore();
}
