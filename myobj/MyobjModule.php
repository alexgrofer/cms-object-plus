<?php

class MyobjModule extends CWebModule
{
	public $controllerNamespace = '\MYOBJ\controllers';
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		Yii::setPathOfAlias('MYOBJ', dirname(__FILE__));

		$routeArr=explode('/', yii::app()->getUrlManager()->parseUrl(yii::app()->getRequest()));
		//только в случае если ввел url,в модульных тестах могут быть проблемы из за этого
		$isAdminUI = ($routeArr[1] == 'admin')?true:false;

		if(!$isAdminUI) {
			if ($routeArr[1] == 'test') {
				$this->setControllerPath(Yii::getPathOfAlias('MYOBJ.controllers.tests'));
			} else {
				$this->setControllerPath(Yii::getPathOfAlias('MYOBJ.controllers.site'));
			}
		}

		yii::app()->setComponents(array(
			'appcms'=>array(
				'class' =>'MYOBJ.components.cms.AppCMS',
				'isAdminUI'=>$isAdminUI,
			)
		));
		yii::app()->appcms->init();
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
