<?php

/**
 * Объявления
 * Class AbsAdverbModel
 */
abstract class AbsAdverbModel extends AbsBaseModel
{
	/**
	 * Название товара
	 * @var
	 */
	public $name;

	/**
	 * Возможные параметры:
	 * возможны опции каталога, необходимо только добавить столбец с схему, (размер обуви)
	 */

	/**
	 * ссылка на католог, какой категории пренадлежит этот товар
	 * @var
	 */
	public $catalog_id;

	/**
	 * Цена товара для объявления фиксированная
	 * @var
	 */
	public $price;
}
