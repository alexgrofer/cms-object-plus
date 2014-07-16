<?php
return array(
	//ТЕСТИРОВАНИЕ абстрактного класса AbsBaseHeaders
	/*
	 * *Строки в которых кранятся данные свойств объектов.
	 *
	 * +id = первичный ключ для таблицы
	 * +property_id = ссылка на тип свойства
	 * +header_id = ссылка на объект
	 * !следующие колонки заполняются в зависимости от типа данных
	 * нельзя заполнить две колонки!
	 * +uptextfield
	 * +upcharfield
	 * +updatetimefield
	 * +upintegerfield
	 * +upfloatfield
	 */

	'table_setcms_uclasses_objproperties_sample_id_1'=>array(
		'id'=>'1',
		'property_id'=>'1', // --> objProperties_sample_id_1
		'header_id'=>'1', // --> AbsBaseHeaders_sample_id_1
		'uptextfield'=>'',
		'upcharfield'=>'type uptextfield1',
		'updatetimefield'=>'',
		'upintegerfield'=>'',
		'upfloatfield'=>'',
	),

	'table_setcms_uclasses_objproperties_sample_id_2'=>array(
		'id'=>'2',
		'property_id'=>'2', // --> objProperties_sample_id_2
		'header_id'=>'1', // --> AbsBaseHeaders_sample_id_1
		'uptextfield'=>'',
		'upcharfield'=>'',
		'updatetimefield'=>'',
		'upintegerfield'=>'',
		'upfloatfield'=>'',
	),

);