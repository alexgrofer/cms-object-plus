<?php
return array(
	/*
	 * * Ассоциация классов
	 * * Дочерняя таблица для связки классов "from_uclasses_id" к классами "from_uclasses_id" MTM
	 * таким образом возможна (ассоциации объектов)
	 *
	 * +id = первичный ключ для таблицы
	 * +from_uclasses_id = id класса
	 * +to_uclasses_id = id класса на которую ссылается класс
	 */

	array(
		'from_uclasses_id'=>'1',
		'to_uclasses_id'=>'2',
	),

	array(
		'from_uclasses_id'=>'2',
		'to_uclasses_id'=>'1',
	),
);