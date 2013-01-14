<?php
if($this->dicturls['action']=='edittempl') $view = '/admin/nav/uihandle';
elseif($this->dicturls['action']!='edit') {
    $view = '/admin/nav/list';
}
$idindexpage = false;
$idelemspush = array();
foreach($_POST as $key => $value) {
    //push
    if(strpos($key, 'push_')!==false && $idindexpage===false) {
        $idindexpage = substr($key,5);
    }
    elseif(strpos($key, 'elemch_')!==false) {
        $idelemspush[] = substr($key,7);
    }
}
if($idindexpage!==false && count($idelemspush)) {
    $modelCRITERIA = new CDbCriteria();
    $modelCRITERIA->addInCondition('id', $idelemspush);
    if(in_array($idindexpage,$idelemspush)===false) {
        $modelAD->updateAll(array('vp1' => $idindexpage),$modelCRITERIA);
    }
    if($this->dicturls['actionid']=='0') {
        $this->redirect($this->getUrlBeforeAction());
    }
    else {
        $this->redirect(Yii::app()->request->url);
    }
}
