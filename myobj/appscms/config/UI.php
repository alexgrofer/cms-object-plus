<?php
$ui = array(
    //создание товаров и категорий для магазина
    //именно этот контроллер будет вызывать разные нужные представления sys/mogazine/orders.php а зависимости от URL
    //сюда не писать представления
    
    //управление каталогом разделов + опций + параметров
    'storedep_catalog' => array(
        'controller' => 'admin/dep_store/CatalogController.php',
        'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    //управление и просмотр товаров + акции
    'storedep_goosd' => array(
        'controller' => 'admin/dep_store/GoodsController.php',
        'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    //управление и просмотр заказов + логистика + статистика
    'storedep_order' => array(
        'controller' => 'admin/dep_store/OrderController.php',
        'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    //почтовые события шаблоны
    'mailevent' => array('controller' => 'sys/mailevent.php',),
    //система заявок от пользователей
    'request' => array('controller' => 'sys/request.php',),
);

$ui_menu = array(
    array('storedep_catalog'),
    array('storedep_goosd'),
    array('storedep_order'),
);