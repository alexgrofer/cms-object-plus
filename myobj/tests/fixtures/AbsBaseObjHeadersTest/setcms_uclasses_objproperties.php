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

	array(
		'from_uclasses_id'=>'1',
		'to_objproperties_id'=>'1',
	),

	array(
		'from_uclasses_id'=>'1',
		'to_objproperties_id'=>'2',
	),

	array(
		'from_uclasses_id'=>'2',
		'to_objproperties_id'=>'2',
	),

	array(
		'from_uclasses_id'=>'3',
		'to_objproperties_id'=>'1',
	),

	array(
		'from_uclasses_id'=>'3',
		'to_objproperties_id'=>'2',
	),

);