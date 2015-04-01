<?php

/**
 * Логистические компании
 * Class AbsLogisticsCompanyModel.php
 */
abstract class AbsLogisticsCompanyModel extends AbsBaseModel
{
	public $name;

	/**
	 * Компания имеет свои точки вызова, компания имеет свою курьерскую службу
	 * @var
	 */
	public $type;
}
