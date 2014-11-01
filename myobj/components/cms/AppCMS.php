<?php
class AppCMS extends CComponent {
	private $isTest=false;
	public function  setIsTest($val) {
		$this->isTest = $val;
	}
	public function getIsTest() {
		return $this->isTest;
	}
	private $config;
	public function getConfig() {
		return $this->config;
	}
	public function init() { //http://yiiframework.ru/doc/guide/ru/extension.create
		//import
		Yii::import('MYOBJ.models.sys.*');
        Yii::import('MYOBJ.components.cms.behaviors.*');
        Yii::import('MYOBJ.components.cms.widgets.*');
		//import include
		Yii::import('MYOBJ.appscms.api.utils',true);
		//set components
		$components = apicms\utils\importRecursName('MYOBJ.appscms.config','components.php',true,true);
		yii::app()->setComponents($components);

		//buils main cms config
		$name_file_config = YII_DEBUG ? 'main.php' : 'main_debug.php';
		$name__admin_file_config = YII_DEBUG ? 'main_admin.php' : 'main_admin_debug.php';
		$this->config = apicms\utils\importRecursName('MYOBJ.appscms.config',$name_file_config,true,true);
		$routeArr=explode('/', yii::app()->getUrlManager()->parseUrl(yii::app()->getRequest()));
		//только в случае если ввел url,в модульных тестах могут быть проблемы из за этого
		if(isset($routeArr[1])) {
			if ($routeArr[1] == 'admin') {
				$this->config = array_merge_recursive($this->config, apicms\utils\importRecursName('MYOBJ.appscms.config', $name__admin_file_config, true, true));
			} else {
				Yii::import('MYOBJ.controllers.cms.AbsSiteController');
			}
		}

		//init preload components
		$preload_components = apicms\utils\importRecursName('MYOBJ.appscms.config','preload.php',true,true);
		foreach($preload_components as $nameComponent) yii::app()->$nameComponent->init();
	}
	//общие методы помошники для системы
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
}
