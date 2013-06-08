<?php
Yii::import('application.modules.myobj.appscms.UCms');
Yii::import('application.modules.myobj.appscms.api.utils',true);
class ObjController extends Controller {
    public $apcms;
    public $layout=false;
    public function run($actionID) {
        $this->apcms = UCms::getInstance($this);
        $paramslisturl = array_slice((explode('/',$this->actionParams['r']) + array('','','','','','','')),2);
        Yii::app()->params['LANGi18n']=$this->apcms->config['language_def'];
        $index = $this->apcms->config['objindexname'];

        if(in_array($paramslisturl[0], $this->apcms->config['languages'])) {
            Yii::app()->params['LANGi18n']=$paramslisturl[0];
            if($paramslisturl[1]) $index = $paramslisturl[1];
        }
        elseif($paramslisturl[0]) {
            $index = $paramslisturl[0];
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
            $this->render('/user/templates/'.$templateobj->vp1);
        }
        else {
            throw new CHttpException(404,'page not is find');
        }
    }
}
