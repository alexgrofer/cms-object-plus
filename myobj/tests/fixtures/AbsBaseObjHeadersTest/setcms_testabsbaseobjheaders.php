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

	'TestAbsBaseObjHeaders_sample_noSave'=>array(
		'id'=>'1',
		'uclass_id'=>'1',
	),
	//test find testGetUProperties
	'TestAbsBaseObjHeaders_sample_id_2'=>array(
		'id'=>'2',
		'uclass_id'=>'1',
	),
	//test find testSetUProperties
	'TestAbsBaseObjHeaders_sample_id_3'=>array(
		'id'=>'3',
		'uclass_id'=>'1',
	),
	//test find testEditlinks
	'TestAbsBaseObjHeaders_sample_id_4'=>array(
		'id'=>'4',
		'uclass_id'=>'1',
	),

	'TestAbsBaseObjHeaders_sample_id_5'=>array(
		'id'=>'5',
		'uclass_id'=>'2',
	),

	'TestAbsBaseObjHeaders_sample_id_6'=>array(
		'id'=>'6',
		'uclass_id'=>'2',
	),
	//test find testAfterSave
	'TestAbsBaseObjHeaders_sample_id_7'=>array(
		'id'=>'7',
		'uclass_id'=>'1',
	),
	//test find testBeforeDelete
	'TestAbsBaseObjHeaders_sample_id_8'=>array(
		'id'=>'8',
		'uclass_id'=>'1',
	),

	'TestAbsBaseObjHeaders_sample_id_9'=>array(
		'id'=>'9',
		'uclass_id'=>'2',
	),
	//test find testSetSetupCriteria
	'TestAbsBaseObjHeaders_sample_id_10'=>array(
		'id'=>'10',
		'uclass_id'=>'3',
	),

	'TestAbsBaseObjHeaders_sample_id_11'=>array(
		'id'=>'11',
		'uclass_id'=>'3',

		'param2'=>'text param2 header 11'
	),

	'TestAbsBaseObjHeaders_sample_id_12'=>array(
		'id'=>'12',
		'uclass_id'=>'3',
	),

	'TestAbsBaseObjHeaders_sample_id_13'=>array(
		'id'=>'13',
		'uclass_id'=>'3',

		'param2'=>'text param2 header 13'
	),

	'TestAbsBaseObjHeaders_sample_id_14'=>array(
		'id'=>'14',
		'uclass_id'=>'3',

		'param1'=>'text param1 header 14'
	),
);