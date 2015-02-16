<?php
class AppCMS extends CComponent {
	private $isAdminUI;
	public function setIsAdminUI() {
		if(is_null($this->isAdminUI)) $this->isAdminUI;
	}
	public function getIsAdminUI() {
		return $this->isAdminUI;
	}

	private $config;

	public function getConfig() {
		return $this->config;
	}

	public function init() { //http://yiiframework.ru/doc/guide/ru/extension.crea;
		Yii::import('MYOBJ.appscms.core.base.*');

		//import
		SysUtils::importRecursName('MYOBJ.appscms.config.all','import_*');
		//const
		SysUtils::importRecursName('MYOBJ.appscms.config.all','const_*');

		$this->config = SysUtils::importRecursName('MYOBJ.appscms.config','main_all.php',true,true);

		$routeArr=explode('/', yii::app()->getUrlManager()->parseUrl(yii::app()->getRequest()));
		//только в случае если ввел url,в модульных тестах могут быть проблемы из за этого
		if(isset($routeArr[1])) {
			if ($routeArr[1] == 'admin') {
					$this->config = array_merge_recursive($this->config, SysUtils::importRecursName('MYOBJ.appscms.config', 'main_admin.php', true, true));
			}
		}
	}
}
