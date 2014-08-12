<?php
return array(
	/*
	 * *Строки в которых кранятся данные свойств объектов.
	 *
	 	'1'=>'upcharfield',
		'2'=>'uptextfield',
		'3'=>'uptextfield',
		'4'=>'updatetimefield',
		'5'=>'uptextfield',
		'6'=>'upcharfield',
		'7'=>'upcharfield',
	 *
	 * +id = первичный ключ для таблицы
	 * +property_id = ссылка на тип свойства
	 * +header_id = ссылка на объект
	 * !следующие колонки заполняются в зависимости от типа данных
	 * нельзя заполнить две колонки!
	 * любая колонка по умолчанию null!
	 * +uptextfield
	 * +upcharfield
	 * +updatetimefield
	 * +upintegerfield
	 * +upfloatfield
	 */

	'table_setcms_uclasses_objproperties_sample_id_1'=>array(
		'property_id'=>'1', // --> objProperties_sample_id_1
		'header_id'=>'1', // --> AbsBaseObjHeaders_sample_id_1
		'upcharfield'=>'type upcharfield1',
	),

);