<?php
return array(
	//ТЕСТИРОВАНИЕ абстрактного класса AbsBaseObjHeaders
	/*
	 * Таблица для стандартных объектов.
	 *
	 * +id = первичный ключ для таблицы
	 * +uclass_id = класса которому принадлежит объект
	 * !возможно добавление любых дополнительных столбцов sql свойственных для класса.
	 *
	 */

	'AbsBaseObjHeaders_sample_id_1'=>array(
		'id'=>'1',
		'uclass_id'=>'1', //фикстура - "uClasses_sample_id_1"
	),

	'AbsBaseObjHeaders_sample_id_2'=>array(
		'id'=>'2',
		'uclass_id'=>'2', //фикстура - "uClasses_sample_id_2"
	),
);