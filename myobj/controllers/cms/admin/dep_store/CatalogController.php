<?php
Yii::import('application.modules.myobj.appscms.api.dep_store.Catalog');
Yii::import('application.modules.myobj.library.utils.clientMemory');
$dsds = new clientMemory();
/// start menu
$str_menu_link='';
if($this->dicturls['paramslist'][2]!='') {
$_ = array(
    'catalog'=>'',
);
foreach($_ as $key => $value) {
    $class='';
    if($this->apcms->isLastUrl($value)) {
        $class='disabled';
    }
$str_menu_link .= '<a class="btn btn-success '.$class.'" href="'.$this->apcms->geturlpage('storedep_catalog', $value).'">'.$key.'</a> ';
}}
$this->setVarRender('str_menu_link',($str_menu_link)?'<div class="label label-info phor2px">'.$str_menu_link.'</div>':'');
/// end menu
if(in_array($this->dicturls['paramslist'][2],array('edit','remove')) && $this->dicturls['paramslist'][3]!='') {
    // REMOVE
    if($this->dicturls['paramslist'][2]=='remove' && (int)$this->dicturls['paramslist'][3]!=0) {
        Catalog::del($this->dicturls['paramslist'][3]);
        $this->redirect($this->apcms->geturlpage('storedep_catalog'));
        Yii::app()->end();
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
        $this->redirect($this->apcms->geturlpage('storedep_catalog'));
    }
    //render param
    $this->setVarRender('form',$form);
    //
    $view = '/admin/dep_store/catalog/obj';
}
else {
    $view = '/admin/dep_store/catalog/list';
}