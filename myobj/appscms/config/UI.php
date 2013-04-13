<?php
$ui = array(
    //Независимые интерфейсы, к примеру вывести график продаж или другой интерфейс. В контроллере можно организовать шаблон, представление и т.д.
    'graphic_sale' => array(
        'controller' => 'admin/dep_store/graphic_sale.php',
        //'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'), //не отображается в меню
        //'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
    ),
);

$ui_menu = array(
    array('graphic_sale'),
);