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
		//buils main cms config
		$name_file_config = YII_DEBUG ? 'main.php' : 'main_debug.php';
		$name__admin_file_config = YII_DEBUG ? 'main_admin.php' : 'main_admin_debug.php';
		$this->config = apicms\utils\importRecursName('MYOBJ.appscms.config',$name_file_config,true,true);
		$routeArr=explode('/', yii::app()->getUrlManager()->parseUrl(yii::app()->getRequest()));
		//только в случае если ввел url,в модульных тестах могут быть проблемы из за этого
		if(isset($routeArr[1])) {
			if ($routeArr[1] == 'admin') {
				$this->config = array_merge_recursive($this->config, apicms\utils\importRecursName('MYOBJ.appscms.config', $name__admin_file_config, true, true));
			}
		}
	}
}
