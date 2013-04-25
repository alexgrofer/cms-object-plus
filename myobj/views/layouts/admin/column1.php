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
$_sl='<a href="'.Yii::app()->createUrl('myobj/admin').'/objects/class/%s">%s</a>';
$_ing_cl=',table-space='.$this->param_contr['current_class_conf_array']['namemodel'].', name-links-model='.$this->param_contr['current_class_conf_array']['namelinksmodel'];
$_ing_cl_ass=',table-space='.$this->param_contr['current_class_ass_spacename']['namemodel'].', name-links-model='.$this->param_contr['current_class_conf_array']['namelinksmodel'];
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
            $arrayheader['class'] = sprintf($_sl,$this->dicturls['paramslist'][4],"'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][4].")".$_ing_cl);
        }
    }
    elseif($this->dicturls['paramslist'][3]=='links') {
        $arrayheader['class'] = sprintf($_sl,$this->dicturls['paramslist'][2],"'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][2].")".$_ing_cl);
        $arrayheader['id-object'] = "'".$this->dicturls['paramslist'][4]."'";
    }
}
elseif($this->dicturls['paramslist'][0]=='class') {
    $arrayheader['class'] = sprintf($_sl,$this->dicturls['paramslist'][1],"'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][1].")".$_ing_cl);
    if(in_array($this->dicturls['paramslist'][3],array('edit','edittempl'))) {
        $arrayheader['id-object'] = "'".$this->dicturls['paramslist'][4]."'";
    }
    elseif($this->dicturls['paramslist'][3]=='lenksobjedit') {
        $arrayheader['assotiation-class'] = sprintf($_sl,$this->dicturls['paramslist'][6],"'".$this->param_contr['current_class_name']."'(".$this->dicturls['paramslist'][6].")".$_ing_cl);
        $arrayheader['class'] = sprintf($_sl,$this->dicturls['paramslist'][1],"'".$this->param_contr['current_class_ass_name']."'(".$this->dicturls['paramslist'][1].")".$_ing_cl_ass);
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