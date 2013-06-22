<?php

$objects_user = array(
    //codename class
    'myObjHeaders' => array(
        'default' => array(
            'cols' => array('id'=>'id','name'=>'name'), //колонки в списке
            //названия гуппы outside identifier
            'groups_read' => null,
            'groups_write' => null,
            'order_by_def' => array('id desc'), //сортировка по умолчанию
        ),
        //headers links example news
        'news' => array(
            'cols' => array('id'=>'id','name'=>'name'),
            'cols_props' => array('text_news'=>'text_news','annotation_news'=>'annotation_news'), //колонки-свойства объектов
            'find' => array('id', 'text_news__prop'), //разрешенные для поиска
            'order_by' => array('id','name', 'text_news__prop', 'annotation_news__prop'), //сортирова в меню
            'order_by_def' => array('annotation_news__prop desc'),
        ),
        'news_section' => array(
            'cols' => array('id'=>'id','name'=>'name'),
            'cols_props' => array('codename_news_section'=>'codename_news_section'),
        ),
    ), //cols
);
/*
 * namemodel -название модели таблицы в которой лежат объекты
 * namelinksmodel - (модель для ссылок) если в классе будут предусмотренны ссылки на другие объекты,
 * если null ссылки для объектов этого табличного пространства не предусмотренны
 */
$set_spaces['1'] = array('namemodel'=>'myObjHeaders','namelinksmodel'=>'linksObjectsAllMy');