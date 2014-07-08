<?php
return array(
	//ТЕСТИРОВАНИЕ класса myObjHeaders
	/*
	 * +uclass_id = класса которому принадлежит объект
	 *
	 * +name = название объекта, к примеру "название новости на сайте"
	 * +content = название объекта, к примеру "текст новости"
	 * +sort = название объекта, к примеру "текст новости"
	 * +bpublic = признак публикации
	 */

	'myObjHeaders_sample_id_1'=>array(
		'id'=>'2',
		'uclass_id'=>'3', //обязательна фикстура - "uClasses_sample_id_1"
		'name'=>'name',
		'content'=>'text text',
		'sort'=>'3',
		'bpublic'=>1,
	),
);