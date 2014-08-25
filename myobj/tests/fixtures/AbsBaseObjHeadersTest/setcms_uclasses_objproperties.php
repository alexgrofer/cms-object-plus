<?php
return array(
	/*
	 * * Дочерняя
	 * * Таблица для связки классов "from_uclasses_id" к свойствам "to_objproperties_id" MTM
	 *
	 * +id = первичный ключ для таблицы
	 * +from_uclasses_id = ссылка на класс
	 * +to_objproperties_id = ссылка на свойство
	 */

	'table_setcms_uclasses_objproperties_sample_1'=>array(
		'from_uclasses_id'=>'1', // --> uClasses_sample_id_1
		'to_objproperties_id'=>'1', // --> objProperties_sample_id_1
	),

	'table_setcms_uclasses_objproperties_sample_2'=>array(
		'from_uclasses_id'=>'1', // --> uClasses_sample_id_1
		'to_objproperties_id'=>'2', // --> objProperties_sample_id_2
	),

	'table_setcms_uclasses_objproperties_sample_3'=>array(
		'from_uclasses_id'=>'2', // --> uClasses_sample_id_1
		'to_objproperties_id'=>'2', // --> objProperties_sample_id_2
	),

);