<?php
Yii::import('application.modules.myobj.appscms.api.dep_store.Catalog');
Yii::import('application.modules.myobj.appscms.api.dep_store.Option');
/// start menu
$str_menu_link='';
$array_catalog = array(
    'catalog'=>array(''),
    'options'=>array('options'),
    'params'=>array('params'),
);
foreach($array_catalog as $key => $value) {
    $class='';
    if($this->apcms->isFirstUrl($key,5)) {
        $class='disabled';
    }
    $str_menu_link .= '<a class="btn btn-success '.$class.'" href="'.$this->apcms->geturlpage('storedep_catalog', $value[0]).'">'.$key.'</a> ';
}unset($array_catalog);
$this->setVarRender('str_menu_link',($str_menu_link)?'<div class="label label-info">'.$str_menu_link.'</div>':'');
/// end menu
switch($this->dicturls['paramslist'][2]) {
/// contoll options
case 'options':
if(in_array($this->dicturls['paramslist'][3],array('edit','remove')) && $this->dicturls['paramslist'][4]!='') {
    // REMOVE
    if($this->dicturls['paramslist'][3]=='remove' && (int)$this->dicturls['paramslist'][4]!=0) {
        Option::del($this->dicturls['paramslist'][4]);
        $this->redirect($this->apcms->geturlpage('storedep_catalog','options'));
    }
    // EDIT, CREATE
    $idobject = (int)$this->dicturls['paramslist'][4] ?: null;
    $objOption = DepstoreOption::model()->findByPk($idobject) ?: (new DepstoreOption());
    $form = $objOption->UserFormModel->initform($_POST);
    if(count($_POST) && $form->validate()) {
        $model = $form->model;
        Option::edit_creat(
            array(
                'name'=>$model->name,
                'type'=>$model->type,
                'range'=>$model->range,
                //'guid'=>'',
            ),
            $idobject
        );
        if($idobject) $this->refresh();
        $this->redirect($this->apcms->geturlpage('storedep_catalog','options'));
    }
    //render param
    $this->setVarRender('form',$form);
    // END
    $view = '/admin/dep_store/catalog/optionsobj';
}
else {
    $view = '/admin/dep_store/catalog/optionslist';
}
break;
/// contoll params
case 'params':
if((in_array($this->dicturls['paramslist'][3],array('edit','remove')) && $this->dicturls['paramslist'][4]!='') || $this->dicturls['paramslist'][4]=='edit') {
    if($this->dicturls['paramslist'][3]=='remove' && (int)$this->dicturls['paramslist'][4]!=0) {
        Option::delParam($this->dicturls['paramslist'][4]);
        $this->redirect($this->apcms->geturlpage('storedep_catalog','params'));
    }
    // EDIT, CREATE
    $idobject = (int)$this->dicturls['paramslist'][5] ?: null;
    $objOption = DepstoreOptionParams::model()->findByPk($idobject) ?: (new DepstoreOptionParams());
    $form = $objOption->UserFormModel->initform($_POST);
    if(count($_POST) && $form->validate()) {
        $model = $form->model;
        Option::edit_creat_param(
            array(
                'val'=>$model->val,
                'id_option'=>$this->dicturls['paramslist'][3],
            ),
            $idobject
        );
        if($idobject) $this->refresh();

        $this->redirect($this->apcms->geturlpage('storedep_catalog','params/option/'.$this->dicturls['paramslist'][3]));
    }
    //render param
    $this->setVarRender('form',$form);
    // END
    $view = '/admin/dep_store/catalog/paramsobj';
}
else {
$view = '/admin/dep_store/catalog/paramslist';
}
break;
/// contoll catalog
default:
if(in_array($this->dicturls['paramslist'][2],array('edit','remove')) && $this->dicturls['paramslist'][3]!='') {
    // REMOVE
    if($this->dicturls['paramslist'][2]=='remove' && (int)$this->dicturls['paramslist'][3]!=0) {
        Catalog::del($this->dicturls['paramslist'][3]);
        $this->redirect($this->apcms->geturlpage('storedep_catalog'));
    }
    // EDIT, CREATE
    
    $idobject = (int)$this->dicturls['paramslist'][3] ?: null;
    $objCatalog = DepstoreCatalog::model()->findByPk($idobject) ?: (new DepstoreCatalog());
    $form = $objCatalog->UserFormModel->initform($_POST);
    if(count($_POST) && $form->validate()) {
        $model = $form->model;
        Catalog::edit_creat(
            array(
                'name'=>$model->name,
                'top'=>$model->top,
                //'guid'=>'',
            ),
            $idobject
        );
        if($idobject) $this->refresh();
        $this->redirect($this->apcms->geturlpage('storedep_catalog'));
        
    }
    //render param
    $this->setVarRender('form',$form);
    //
    $view = '/admin/dep_store/catalog/catalogobj';
}
else {
    $view = '/admin/dep_store/catalog/cataloglist';
}
}
