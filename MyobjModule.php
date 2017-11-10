<?php

class MyobjModule extends CWebModule
{
	public static function createCMS() {
		Yii::setPathOfAlias('MYOBJ', dirname(__FILE__));

		$isAdminUI = false;
		if(IS_CONSOLE==false) {
			$routeArr = explode('/', yii::app()->getUrlManager()->parseUrl(yii::app()->getRequest()));
			//только в случае если ввел url,в модульных тестах могут быть проблемы из за этого
			$isAdminUI = (isset($routeArr[1]) && $routeArr[1] == 'admin') ? true : false;
		}

		yii::app()->setComponents(array(
			'appcms'=>array(
				'class' =>'MYOBJ.components.cms.AppCMS',
				'isAdminUI'=>$isAdminUI,
			)
		));
		yii::app()->appcms->init();
	}

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		static::createCMS();
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
