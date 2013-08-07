<?php
class AppCMS extends CComponent {
	private $testprop;
	public function  setTestprop($val) {
		$this->testprop = $val;
	}
	public function getTestprop() {
		return $this->testprop;
	}
	public function init() {
		//при инициализации компонента в модуле
		//исходя из настроек что то сделать проинсталлировать модули и т.д
		//if(YII_DEBUG) {
			//$name_config = '/config/main_debug.php';
		//
	}
	//общие методы помошники для системы
	public function getparams() {
		if(!count($this->_paramsgetnav)) {
			Yii::import('application.modules.myobj.appscms.api.utils',true);
			$this->_paramsgetnav = apicms\utils\uiparamnav($this->setparams['OBJNAV']->content);
		}
		return $this->_paramsgetnav;
	}
	public function geturlpage($name='',$addelem='',$params=array()) {
		$parse = array_slice(explode('/', $this->_controller->actionParams['r']),1);
		//last search array_keys($parse,$name)
		$keylast = (count(($_=array_keys($parse,$name))))?$_[count($_)-1]:count($parse)-1;
		$str_slice = implode('/',array_slice($parse,0,$keylast+1));
		//
		return $this->_controller->createUrl($str_slice.'/'.$addelem,$params);
	}
	function isFirstUrl($strlsturl,$start=4) {
		echo implode('/',array_slice(explode('/',$this->_controller->actionParams['r']),$start));
		if($strlsturl!='' && preg_match('~^'.$strlsturl.'~',implode('/',array_slice(explode('/',$this->_controller->actionParams['r']),$start)))) {
			return true;
		}
		return false;
	}

	private $_thisnavhandles = null;
	public function handle($name, $id) {
		if($this->_thisnavhandles==null) {
			$this->_thisnavhandles = array();
			$handles = $this->setparams['OBJNAV']->getobjlinks('handle_sys')->findAll();
			$idsview = array();
			foreach($handles as $handle) {
				$idsview[] = $handle->vp1; //vp1 = idview , sort = handletmplid
			}
			$CRITERIA = new CDbCriteria();
			$CRITERIA->addInCondition('id', $idsview);
			$listview = uClasses::getclass('views_sys')->objects()->findAll($CRITERIA);
			$elemsview = array();
			foreach($listview as $view) {
				$elemsview[$view->id] = array('patchview' => $view->vp1, 'objview' => $view); //vp1 = patchview
			}
			foreach($handles as $handle) {
				$this->_thisnavhandles[$handle->sort] = $elemsview[$handle->vp1];
			}
		}

		$patchview = apicms\utils\URender($id,$this->_thisnavhandles);
		if($patchview!='') {
			Yii::beginProfile('Loade_handle_v:'.$patchview);

			$render =  $this->_controller->renderPartial('/../views/cms/views/'.$patchview);
			Yii::endProfile('Loade_handle_v:'.$patchview);
			return $render;

		}
	}
}
