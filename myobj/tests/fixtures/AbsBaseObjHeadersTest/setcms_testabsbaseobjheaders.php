<?php
return array(
	/*
	 * Таблица для стандартных объектов.
	 *
	 * +id = первичный ключ для таблицы
	 * +uclass_id = класса которому принадлежит объект
	 * !возможно добавление любых дополнительных столбцов sql свойственных для класса.
	 *
	 */

	'TestAbsBaseObjHeaders_sample_id_1'=>array(
		'id'=>'1',
		'uclass_id'=>'1', //фикстура - "uClasses_sample_id_1"
	),

	'TestAbsBaseObjHeaders_sample_id_2'=>array(
		'id'=>'2',
		'uclass_id'=>'2', //фикстура - "uClasses_sample_id_2"
	),

	'TestAbsBaseObjHeaders_sample_id_3'=>array(
		'id'=>'3',
		'uclass_id'=>'3', //фикстура - "uClasses_sample_id_2"
	),
	'TestAbsBaseObjHeaders_sample_id_4'=>array(
		'id'=>'4',
		'uclass_id'=>'3', //фикстура - "uClasses_sample_id_2"
	),

	'TestAbsBaseObjHeaders_sample_id_5'=>array(
		'id'=>'5',
		'uclass_id'=>'4', //фикстура - "uClasses_sample_id_4"
	),
	'TestAbsBaseObjHeaders_sample_id_6'=>array(
		'id'=>'6',
		'uclass_id'=>'4', //фикстура - "uClasses_sample_id_4"
	),
);