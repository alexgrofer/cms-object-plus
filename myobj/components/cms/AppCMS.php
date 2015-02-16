<?php
class AppCMS extends CComponent {
	private $isAdminUI;
	public function setIsAdminUI() {
		if(is_null($this->isAdminUI)) $this->$isAdminUI;
	}
	public function getIsAdminUI() {
		return $this->$isAdminUI;
	}

	private $config;

	public function getConfig() {
		return $this->config;
	}

	public function init() { //http://yiiframework.ru/doc/guide/ru/extension.create
		Yii::import('MYOBJ.appscms.api.utils', true);

		$folder_conf = $this->isAdminUI?'admin':'user';
		//import
		apicms\utils\importRecursName('MYOBJ.appscms.config.'.$folder_conf,'import_*');
		//const
		apicms\utils\importRecursName('MYOBJ.appscms.config.'.$folder_conf,'const_*');
	}
}
