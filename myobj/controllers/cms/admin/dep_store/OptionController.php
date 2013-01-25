<?php
/// start menu
Yii::import('application.modules.myobj.appscms.api.dep_store.Option');

$str_menu_link='';
if($this->dicturls['paramslist'][2]!='') {
$_ = array(
    'options list'=>'',
);
foreach($_ as $key => $value) {
    $class='';
    if($this->apcms->isLastUrl($value)) {
        $class='disabled';
    }
$str_menu_link .= '<a class="btn btn-success '.$class.'" href="'.$this->apcms->geturlpage('storedep_option', $value).'">'.$key.'</a> ';
}}
$this->setVarRender('str_menu_link',($str_menu_link)?'<div class="label label-info phor2px">'.$str_menu_link.'</div>':'');
/// end menu
if(in_array($this->dicturls['paramslist'][2],array('edit','remove')) && $this->dicturls['paramslist'][3]!='') {
    // REMOVE
    if($this->dicturls['paramslist'][2]=='remove' && (int)$this->dicturls['paramslist'][3]!=0) {
        Option::del($this->dicturls['paramslist'][3]);
        $this->redirect($this->apcms->geturlpage('storedep_option'));
        Yii::app()->end();
    }
    // EDIT, CREATE
    $idobject = (int)$this->dicturls['paramslist'][3] ?: null;
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
        $this->redirect($this->apcms->geturlpage('storedep_option'));
    }
    //render param
    $this->setVarRender('form',$form);
    // END
    $view = '/admin/dep_store/option/obj';
}
elseif($this->dicturls['paramslist'][2]=='params' && $this->dicturls['paramslist'][3]!='') {
    $view = '/admin/dep_store/option/listparams';
    if($this->dicturls['paramslist'][4]=='edit' && $this->dicturls['paramslist'][5]!='') {
        //подключаем в форму модель редактирования 
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
            $this->redirect($this->apcms->geturlpage('storedep_option', 'params/'.$this->dicturls['paramslist'][3]));
        }
        //render param
        $this->setVarRender('form',$form);
        // END
        $view = '/admin/dep_store/option/objparams';
    }
}
else {
    $view = '/admin/dep_store/option/list';
}
