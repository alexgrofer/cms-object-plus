<?php

/**
 * из модуля миграции делать так:
 * 		php yiic migrate create migrate_init --migrationPath=application.modules.myobj.migrations
 * 		php yiic migrate --migrationPath=application.modules.myobj.migrations
 * Class m150421_083653_migrate_init
 */
class m150421_083653_migrate_init extends CDbMigration
{
	public function safeUp()
	{
		/*
		$this->insert('test', array(
			'name'=>'test',
		));
		Yii::app()->db->getLastInsertID(); //последняя запись
		*/
	}

	public function safeDown()
	{
		//
	}
}