<?php

class MyobjModule extends CWebModule
{
	public $controllerNamespace = '\MYOBJ\controllers';
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		Yii::setPathOfAlias('MYOBJ', dirname(__FILE__));

		// import the module-level models and components
		$this->setImport(array(
			'MYOBJ.models.*',
			'MYOBJ.components.*',
		));
		yii::app()->setComponents(array(
			'appcms'=>array(
				'class' =>'MYOBJ.components.cms.AppCMS',
				'isTest'=>false,
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
