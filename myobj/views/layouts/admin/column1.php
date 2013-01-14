<?php $this->beginClip('header')?>
<style>
.headermen {border: 1px solid #000; padding: 0 5px}
</style>
<?php
$arrayheader = array(
    'relation-model' => '',
    'model' => '',
    'assotiation-class' => '',
    'class' => '',
    'id-object' => '',
);
if($this->dicturls['paramslist'][0]=='models' && $this->dicturls['paramslist'][1]!='') {
    $arrayheader['model'] = "'".$this->dicturls['paramslist'][1]."'";
    if($this->dicturls['paramslist'][3]=='edit') {
        $arrayheader['id-object'] = "'".$this->dicturls['paramslist'][4]."'";
    }
    elseif($this->dicturls['paramslist'][3]=='relationobj') {
        $arrayheader['model'] = "'".$this->dicturls['paramslist'][7]."'";
        $arrayheader['id-object'] = "'".$this->dicturls['paramslist'][4]."'";
        $arrayheader['relation-model'] = "'".$this->dicturls['paramslist'][1]."'";
        if(true) {
            $arrayheader['id-object'] = '';
            $arrayheader['class'] = "'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][4]."), table-space='".$this->param_contr['current_class_spacename']."'";
        }
    }
    elseif($this->dicturls['paramslist'][3]=='links') {
        $arrayheader['class'] = "'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][2]."), table-space='".$this->param_contr['current_class_spacename']."'";
        $arrayheader['id-object'] = "'".$this->dicturls['paramslist'][4]."'";
    }
}
elseif($this->dicturls['paramslist'][0]=='class') {
    $arrayheader['class'] = "'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][1]."), table-space='".$this->param_contr['current_class_spacename']."'";
    if(in_array($this->dicturls['paramslist'][3],array('edit','edittempl'))) {
        $arrayheader['id-object'] = "'".$this->dicturls['paramslist'][4]."'";
    }
    elseif($this->dicturls['paramslist'][3]=='lenksobjedit') {
        $arrayheader['assotiation-class'] = "'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][6]."), table-space='".$this->param_contr['current_class_spacename']."'";
        $arrayheader['class'] = "'".$this->param_contr['current_class_ass_name']."'(".$this->dicturls['paramslist'][1]."), table-space='".$this->param_contr['current_class_ass_spacename']."'";
        $arrayheader['id-object'] = "'".$this->dicturls['paramslist'][4]."'";
    }
}
foreach($arrayheader as $key => $val) {
    if($val!='') {
        echo '<code class="headermen"><b>'.$key.'</b>: '.$val.'</code>';
    }
}
/*
нужно узнать урл как узнать??
дальше просматриваем его и в зависимости от строения посать хидер
*/
?>
<?php $this->endClip() ?>
<?php $this->beginContent('/layouts/admin/main'); ?>

<?php echo $content; ?>

<?php $this->endContent(); ?>