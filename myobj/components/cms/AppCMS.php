<?php
use MYOBJ\appscms\core\base\SysUtils;

class AppCMS extends CComponent {
	private $isAdminUI;
	public function setIsAdminUI($isAdminUI) {
		if(is_null($this->isAdminUI)) $this->isAdminUI = $isAdminUI;
	}
	public function getIsAdminUI() {
		return $this->isAdminUI;
	}

	private $config;

	public function getConfig() {
		return $this->config;
	}

	public function init() { //http://yiiframework.ru/doc/guide/ru/extension.crea;
		//import
		SysUtils::importRecursName('MYOBJ.appscms.config.all','import_*');
		//const
		SysUtils::importRecursName('MYOBJ.appscms.config.all','const_*');
		//components
		SysUtils::importRecursName('MYOBJ.appscms.config.all','init_components_*');

		$this->config = SysUtils::importRecursName('MYOBJ.appscms.config','main_all.php',true,true);

		if($this->isAdminUI) {
			//import
			SysUtils::importRecursName('MYOBJ.appscms.config.admin','import_*');
			//const
			SysUtils::importRecursName('MYOBJ.appscms.config.admin','const_*');

			$this->config = array_merge_recursive($this->config, SysUtils::importRecursName('MYOBJ.appscms.config', 'main_admin.php', true, true));
		}
	}
}
