<?php

/**
 * Магазин, точка выдачи
 * Class AbsStoreModel
 */
abstract class AbsStoreModel extends AbsBaseModel
{

	public $name;

	/**
	 * Страна в которой находится магазин
	 * @var
	 */
	public $country;

	/**
	 * Город в котором находится магазин
	 * @var
	 */
	public $city;

	/**
	 * Адрес магазина
	 * @var
	 */
	public $address;

	/**
	 * Описание магазина
	 * @var
	 */
	public $desc;
}
