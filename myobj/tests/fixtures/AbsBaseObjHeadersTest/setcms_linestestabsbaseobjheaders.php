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

	array(
		'property_id'=>'1',
		'header_id'=>'2',
		'upcharfield'=>'type upcharfield line1 header 2',
	),

	array(
		'property_id'=>'2',
		'header_id'=>'2',
		'uptextfield'=>'type uptextfield line2 header 2',
	),

	array(
		'property_id'=>'1',
		'header_id'=>'3',
		'upcharfield'=>'type upcharfield line3 header 3',
	),

	array(
		'property_id'=>'2',
		'header_id'=>'3',
		'uptextfield'=>'type uptextfield line4 header 3',
	),

	array(
		'property_id'=>'2',
		'header_id'=>'9',
		'uptextfield'=>'type uptextfield line5 header 9',
	),

	array(
		'property_id'=>'1',
		'header_id'=>'10',
		'upcharfield'=>'h type upcharfield line1 header 10',
	),

	array(
		'property_id'=>'2',
		'header_id'=>'10',
		'uptextfield'=>'g type uptextfield line2 header 10',
	),

	array(
		'property_id'=>'1',
		'header_id'=>'11',
		'upcharfield'=>'f type upcharfield line1 header 11',
	),

	array(
		'property_id'=>'1',
		'header_id'=>'12',
		'upcharfield'=>'e type upcharfield line1 header 12',
	),

	array(
		'property_id'=>'2',
		'header_id'=>'12',
		'uptextfield'=>'d type uptextfield line2 header 12',
	),

	array(
		'property_id'=>'2',
		'header_id'=>'13',
		'uptextfield'=>'b type uptextfield line2 header 13',
	),

	array(
		'property_id'=>'1',
		'header_id'=>'14',
		'upcharfield'=>'d type upcharfield line1 header 10',
	),

);