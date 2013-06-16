<?php

$objects_user = array(
    //codename class
    'myObjHeaders' => array(
        'default' => array(
            'cols' => array('id'=>'id','name'=>'name'),
            'groups_read' => null,
            'groups_write' => null,
        ),
        //headers links example news
        'news' => array(
            'cols' => array('id'=>'id','name'=>'name'),
            'cols_props' => array('text_news'=>'text_news','annotation_news'=>'annotation_news'),
            'order_by' => array('id desc'),
        ),
        'news_section' => array(
            'cols' => array('id'=>'id','name'=>'name'),
            'cols_props' => array('codename_news_section'=>'codename_news_section'),
            'order_by' => array('id desc'),
        ),
    ), //cols
);

$set_spaces['1'] = array('namemodel'=>'myObjHeaders','namelinksmodel'=>'linksObjectsAllMy');
$set_spaces['3'] = array('namemodel'=>'storedepObjHeaders','namelinksmodel'=>null);