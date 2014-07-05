<?php
return array(
	//ТЕСТИРОВАНИЕ абстрактного класса AbsBaseHeaders
	/*
	 * +uclass_id = класса которому принадлежит объект
	 */

	'AbsBaseHeaders_sample_id_1'=>array(
		'id'=>'1',
		'uclass_id'=>'1', //обязательна фикстура - "uClasses_sample_id_1"
	),

	//ТЕСТИРОВАНИЕ класса myObjHeaders
	/*
	 * +name = название объекта, к примеру "название новости на сайте"
	 * +content = название объекта, к примеру "текст новости"
	 * +sort = название объекта, к примеру "текст новости"
	 * +bpublic = признак публикации
	 */

	'myObjHeaders_sample_id_2'=>array(
		'id'=>'2',
		'uclass_id'=>'3', //обязательна фикстура - "uClasses_sample_id_3"
		'name'=>'name',
		'content'=>'text text',
		'sort'=>'3',
		'bpublic'=>1,
	),
);