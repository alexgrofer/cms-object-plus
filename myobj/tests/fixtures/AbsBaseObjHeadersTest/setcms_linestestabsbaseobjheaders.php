<?php
return array(
	/*
	 * * Строки объекта.
	 * * Свойства объекта заполненны строками.
	 *
	 * +id = первичный ключ для таблицы
	 * +property_id = ссылка на тип свойства
	 * +header_id = ссылка на объект
	 *
	 * !следующие колонки заполняются в зависимости от типа данных up.....
	 * 1 строка это одно свойство!!!
	 * любая колонка по умолчанию null!
	 * +uptextfield
	 * +upcharfield
	 * +updatetimefield
	 * +upintegerfield
	 * +upfloatfield
	 */

	'linesTestAbsBaseObjHeaders_sample_1'=>array(
		'property_id'=>'1', // --> objProperties_sample_id_1
		'header_id'=>'2', // --> AbsBaseObjHeaders_sample_id_2
		'upcharfield'=>'type upcharfield line1 header 2',
	),

	'linesTestAbsBaseObjHeaders_sample_2'=>array(
		'property_id'=>'2', // --> objProperties_sample_id_2
		'header_id'=>'2', // --> AbsBaseObjHeaders_sample_id_2
		'uptextfield'=>'type uptextfield line2 header 2',
	),

	'linesTestAbsBaseObjHeaders_sample_3'=>array(
		'property_id'=>'1', // --> objProperties_sample_id_1
		'header_id'=>'3', // --> AbsBaseObjHeaders_sample_id_3
		'upcharfield'=>'type upcharfield line3 header 3',
	),

	'linesTestAbsBaseObjHeaders_sample_4'=>array(
		'property_id'=>'2', // --> objProperties_sample_id_2
		'header_id'=>'3', // --> AbsBaseObjHeaders_sample_id_3
		'uptextfield'=>'type uptextfield line4 header 3',
	),

	'linesTestAbsBaseObjHeaders_sample_5'=>array(
		'property_id'=>'2', // --> objProperties_sample_id_2
		'header_id'=>'9', // --> AbsBaseObjHeaders_sample_id_10
		'uptextfield'=>'type uptextfield line5 header 9',
	),

);