<?php

$templ = '<p><a href="'.$this->dicturls['admin'].'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/%s">%s</a></p>';

$menumodelui = $this->apcms->config[($this->dicturls['paramslist'][0]=='models')?'menumodel':'menuui'];

$arrelems = ($this->dicturls['paramslist'][0]=='models')?$this->apcms->config['controlui']['objects']['models']:$this->apcms->config['controlui']['ui'];

if(count($menumodelui)) {
    foreach($menumodelui as $arrname) {
        if(array_key_exists('groups_read', $arrelems[$arrname[0]]) && !(array_intersect(Yii::app()->user->groupsident, $arrelems[$arrname[0]]['groups_read']))) continue;
        printf($templ,$arrname[0],$arrname[0]);
    }
}
