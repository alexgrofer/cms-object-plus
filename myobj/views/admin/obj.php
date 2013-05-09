<?php
if(!$REND_acces_write) {
    //должно делаться в контроллере - или добавить мини контроллер ?
    echo '<p class="alert">not acces edit</p>';
}
$htmldiv = '<div%s>%s</div>';
$htmlp='<p class="%s">%s</p>';
$htmlspan='<span class="%s">%s</span>';
$htmlinput='<input type="%s" name="%s" value="%s" class="%s" />';

//потом убрать переменную в контроллер, название поля не нужно хранить в настройках оно уже и так есть в настройке реляции
$REND_addElem=array();
if($this->dicturls['paramslist'][5]=='selfobjrelation') {
    $array_names_v_mtm = array();
    $nameps_mtm = '_col_mtm_model';

    //смотрим в конфиге какие колонки из дочерней таблице показываем при selfobjrelation
    foreach($this->apcms->config['controlui']['objects']['models'][$this->dicturls['paramslist'][1]]['selfobjrelationElements'][$this->dicturls['paramslist'][8]] as $namer) {
        //убрать из цикла
        $SelectArr = $REND_model->getMTMcol($this->dicturls['paramslist'][8],$this->dicturls['paramslist'][6],$namer);
        $REND_addElem[]=array('name'=>$namer.$nameps_mtm, 'def_value'=>$SelectArr[$namer]);
        $array_names_v_mtm[$namer] = $SelectArr[$namer];
    }


}
//редактирование представлений и шаблонов
//опять же перенести все в контроллер, или создать отдельный контроллер еси нужно и присоеденить к текущему контроллеру возможно присоединять контроллеры?
if(in_array($this->param_contr['current_class_name'],array('templates_sys','views_sys'))) {
    $namefilderfile = ($this->param_contr['current_class_name']=='templates_sys')?'templates':'views';
    $namefile = dirname(__FILE__).'/../user/'.$namefilderfile.'/'.$REND_model->vp1.'.php';
    if(file_exists($namefile)) {
        $contenttext=file_get_contents($namefile);
    }
    $snamefile = 'edit_file_template';
    $REND_addElem[] = array('name'=>$snamefile, 'def_value'=>isset($contenttext)?$contenttext:'', 'type'=>'textarea');
}


//задача такова что мы для нового элемента тоже можем установить параметры
$form = $REND_model->UserFormModel->initform($_POST,$REND_editform,$REND_addElem);
$form->attributes = array('enctype' => 'multipart/form-data');
echo $form;

if(count($_POST) && $form->validate()) {
    $REND_model->save();

    if(isset($array_names_v_mtm) && $this->dicturls['actionid']=='0') {
        $REND_model->addMTObjects($this->dicturls['paramslist'][8],array($this->dicturls['paramslist'][6]));
    }


    foreach($_POST['EmptyForm'] as $key => $val) {

        //если существует параметр в запросе и подходит под $nameps_mtm, если есть в списке конфигурации $array_names_v_mtm,если не равен предыдущему значению
        //изменение данных в дочерней таблице при selfobjrelation, если есть параметры в конфиге вообще
        if(isset($array_names_v_mtm) && count($array_names_v_mtm)) {
            $array_edit_post_mtmparam = array();
            if(($pos = strpos($key,$nameps_mtm)) && array_key_exists(($name_norm = substr($key,0,$pos)),$array_names_v_mtm) && (array_key_exists($name_norm, $array_names_v_mtm) && $array_names_v_mtm[$name_norm]!=$val)) {
                $array_edit_post_mtmparam[$name_norm] = trim($val);
            }
        }
        //редактирование файлов
        if(isset($namefile) && strpos($key,$snamefile)!==false) {
            file_put_contents($namefile, $val);
        }

    }
    if(isset($array_edit_post_mtmparam) && count($array_edit_post_mtmparam)) {
        $REND_model->setMTMcol($this->dicturls['paramslist'][8],array($this->dicturls['paramslist'][6]),$array_edit_post_mtmparam);
    }



    if($this->dicturls['actionid']=='0') {
        $this->redirect($this->getUrlBeforeAction());
    }
    else {
        $this->redirect(Yii::app()->request->url);
    }
}

?>
<style>
.errorMessage {color: red;padding-bottom: 15px}
</style>