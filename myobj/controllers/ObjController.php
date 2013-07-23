<?php
Yii::import('application.modules.myobj.appscms.UCms');
Yii::import('application.modules.myobj.appscms.api.utils',true);
class ObjController extends Controller {
    public $apcms;
    public $layout=false;
    public $dicturls = array();
    public function run($actionID) {
        $this->apcms = UCms::getInstance($this);
        $this->dicturls['paramslist'] = array_slice((explode('/',$this->actionParams['r']) + array('','','','','','','')),2);
        $this->dicturls['all'] = '/'.$this->actionParams['r'];

        Yii::app()->params['LANGi18n']=$this->apcms->config['language_def'];
        $index = $this->apcms->config['objindexname'];

        if(in_array($this->dicturls['paramslist'][0], $this->apcms->config['languages'])) {
            Yii::app()->params['LANGi18n']=$this->dicturls['paramslist'][0];
            if($this->dicturls['paramslist'][1]) $index = $this->dicturls['paramslist'][1];
        }
        elseif($this->dicturls['paramslist'][0]) {
            $index = $this->dicturls['paramslist'][0];
        }
        //насколько важно менять общий язык серды?
        //Yii::app()->setLanguage(Yii::app()->params['LANGi18n']);

        $findparamname = (preg_match('/\D/', $index))?'vp2':'id';
        $objnav = uClasses::getclass('navigation_sys')->objects()->findByAttributes(array($findparamname => $index, 'bp1' => true));

        if($objnav) {
            //если нет объекта шаблона привязанного к этому объекту навигации
            if(!($templateobj = $objnav->getobjlinks('templates_sys')->find())) {
                throw new CException(Yii::t('cms','none object template'));
            }
            $this->apcms->setparams['OBJNAV'] = $objnav;
            $conf_site = array();
            if($namecontroller=$objnav->getobjlinks('controllersnav_sys')->find()) {
                require(dirname(__FILE__).'/cms/user/'.$namecontroller->vp1);
            }
            $this->layout='/user/templates/'.$templateobj->vp1;
            $this->render('/user/templates/'.$templateobj->vp1.'_content',$conf_site);
        }
        else {
            throw new CHttpException(404,'page not is find');
        }
    }
}
