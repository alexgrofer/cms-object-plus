<?php


abstract class AbsAdvertModel extends AbsBaseModel
{
	/**
	 * Название товара
	 * @var
	 */
	public $name;
	public $date_time_create;
	public $catalog_id;
	public $price;
	public $is_public;
	public $user_id;
}
