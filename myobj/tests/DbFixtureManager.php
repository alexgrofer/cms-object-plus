<?php
Yii::import('system.test.CDbFixtureManager');

class DbFixtureManager extends CDbFixtureManager
{
	public function init()
	{
		$this->basePath=Yii::app()->assetManager->basePath;

		parent::init();
	}
}