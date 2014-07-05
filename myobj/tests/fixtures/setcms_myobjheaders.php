<?php
/*
 * +uclass_id = класса которому принадлежит объект
 * =НЕ ОБЯЗАТЕЛЬНЫ ДЛЯ СИСТЕМЫ:
 * +name = название объекта, к примеру "название новости на сайте"
 * +content = название объекта, к примеру "текст новости"
 * +sort = название объекта, к примеру "текст новости"
 * +bpublic = признак публикации
 */
return array(
	//запись для теста AbsBaseHeaders
	'AbsBaseHeadersTest_sample_id_1'=>array(
		'id'=>1,
		'uclass_id'=>'1', //запись из фикстуры - "uClassesTest_sample_id_1"
		'name'=>'name1',
		'content'=>'text',
		'sort'=>1,
		'bpublic'=>1,
	),
);