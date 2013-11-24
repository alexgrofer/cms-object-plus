<?php
namespace MYOBJ\controllers;
use \yii as yii;

class ObjController extends \Controller {
	public $apcms;
	public $layout=false;
	public $dicturls = array();
	public function run($actionID) {
		$this->dicturls['paramslist'] = array_slice((explode('/',Yii::app()->request->getParam('r')) + array('','','','','','','')),2);
		$this->dicturls['all'] = '/'.Yii::app()->request->getParam('r');

		Yii::app()->params['LANGi18n']=Yii::app()->appcms->config['language_def'];
		$index = Yii::app()->appcms->config['objindexname'];

		if(in_array($this->dicturls['paramslist'][0], Yii::app()->appcms->config['languages'])) {
			Yii::app()->params['LANGi18n']=$this->dicturls['paramslist'][0];
			if($this->dicturls['paramslist'][1]) $index = $this->dicturls['paramslist'][1];
		}
		elseif($this->dicturls['paramslist'][0]) {
			$index = $this->dicturls['paramslist'][0];
		}
		//насколько важно менять общий язык серды?
		//Yii::app()->setLanguage(Yii::app()->params['LANGi18n']);

		$findparamname = (preg_match('/\D/', $index))?'vp2':'id';
		$objnav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array($findparamname => $index, 'bp1' => true));

		if($objnav) {
			//если нет объекта шаблона привязанного к этому объекту навигации
			if(!($templateobj = $objnav->getobjlinks('templates_sys')->find())) {
				throw new CException(Yii::t('cms','none object template'));
			}
			Yii::app()->params['OBJNAV'] = $objnav;
			Yii::app()->params['OBJPARENT_CONTROLLER'] = $this;
			$conf_site = array();
			if($namecontroller=$objnav->getobjlinks('controllersnav_sys')->find()) {
				require(dirname(__FILE__).'/cms/user/'.$namecontroller->vp1);
			}
			$this->layout='/cms/templates/'.$templateobj->vp1;
			$this->render('/cms/templates/'.$templateobj->vp1.'_content',$conf_site);
		}
		else {
			throw new \CHttpException(404,'page not is find');
		}
	}
}
