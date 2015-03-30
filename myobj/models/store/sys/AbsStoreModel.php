<?php

/**
 * Склад или магазин, точка выдачи
 * Class AbsStoreModel
 */
abstract class AbsStoreModel extends AbsBaseModel
{

	public $name;

	/**
	 * дата отгрузки в этот магазин
	 * идентификатор кампании в системе если это агрегатор
	 */
}
