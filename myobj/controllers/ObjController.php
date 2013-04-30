<?php
Yii::import('application.modules.myobj.appscms.UCms');
Yii::import('application.modules.myobj.appscms.api.utils',true);
class ObjController extends Controller {
    public $apcms;
    public $layout=false;
    public function run($actionID) {
        $this->apcms = UCms::getInstance($this);
        $paramslisturl = array_slice((explode('/',$this->actionParams['r']) + array('','','','','','','')),2);
        $lastindnav = $actionID;
        foreach($paramslisturl as $name) {
            //last name index obj
            if($name) $lastindnav = $name;
        }
        Yii::app()->params['LANGi18n']=$this->apcms->config['language_def'];
        if(in_array($paramslisturl[0], $this->apcms->config['languages'])) {
            Yii::app()->params['LANGi18n']=$paramslisturl[0];
        }
        // -- example
        //config['language_def'] = ru
        //set params['LANGi18n'] = en
        //create - protected/messages/ru/app.php //or magazine.php
        //code - return array('город'=>'city')
        //echo user view or template - Yii::t('app', 'город'); //or magazine
        
        Yii::app()->setLanguage(Yii::app()->params['LANGi18n']);
        if(!$lastindnav || in_array($lastindnav,$this->apcms->config['languages'])) {
            $lastindnav = $this->apcms->config['objindexname'];
        }
        $findparamname = ((int)$lastindnav)?'id':'vp2';
        $objnav = uClasses::getclass('navigation_sys')->objects()->findByAttributes(array($findparamname => $lastindnav, 'bp1' => true));
        
        if($objnav) {
            //если нет объекта шаблона привязанного к этому объекту навигации
            if(!($templateobj = $objnav->getobjlinks('templates_sys')->count())) {
                throw new CException(Yii::t('cms','none object template'));
            }
            $this->apcms->setparams['OBJNAV'] = $objnav;
            $this->render('/user/templates/'.$templateobj[0]->vp1);
        }
        else {
            throw new CHttpException(404,'page not is find');
        }
    }
}
