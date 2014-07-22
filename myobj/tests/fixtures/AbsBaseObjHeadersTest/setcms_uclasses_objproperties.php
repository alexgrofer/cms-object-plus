<?php
return array(
	/*
	 * * Дочерняя таблица для связки классов "from_uclasses_id" к свойствам "to_objproperties_id" MTM
	 *
	 * +id = первичный ключ для таблицы
	 * +from_uclasses_id = ссылка на класс
	 * +to_objproperties_id = ссылка на свойство
	 */

	'table_setcms_uclasses_objproperties_sample_id_1'=>array(
		'id'=>'1',
		'from_uclasses_id'=>'1', // --> uClasses_sample_id_1
		'to_objproperties_id'=>'1', // --> objProperties_sample_id_1
	),

	'table_setcms_uclasses_objproperties_sample_id_2'=>array(
		'id'=>'2',
		'from_uclasses_id'=>'1', // --> uClasses_sample_id_2
		'to_objproperties_id'=>'2', // --> objProperties_sample_id_2
	),

);