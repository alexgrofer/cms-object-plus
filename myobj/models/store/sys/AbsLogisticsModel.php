<?php

/**
 * Логистическая таблица доставки
 * Class AbsLogisticsModel
 */
abstract class AbsLogisticsModel extends AbsBaseModel
{
	/**
	 * id логистической компании как своей так и партнера (например PickPoint)
	 * @var
	 */
	public $company_id;

	/**
	 * Модель логистической компании
	 * @return mixed
	 */
	abstract public function getModelCompanyLogistic();

	/**
	 * адресс
	 * статус отгрузки
	 * id заказа
	 * пояснение к доставке
	 * дата создания
	 * дата отгрузки
	 * дата доставки
	 * причина отказа
	 * стоимость доставки
	 * имя
	 */
}
