<?php
//запрет на удаление определенных классов и объектов, свойств
$none_del = array(
    'classes' => array(
        'groups_sys', 'templates_sys', 'views_sys', 'handle_sys', 'navigation_sys', 'param_sys',
    ),
    'objects' => array(
        'groups_sys' => array(
            'vp2'=>'admincms',
            'vp2'=>'guestsys',
            'vp2'=>'authorizedsys',
        ),
    ),
    'prop' => array(),
);