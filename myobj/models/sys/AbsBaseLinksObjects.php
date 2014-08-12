<?php
abstract class AbsBaseLinksObjects extends AbsBaseModel
{
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

	/**
	 * ключ объекта заголовка
	 * @var int
	 */
	public $idobj;

	public function relations()
	{
		//привязка ссылок, сами на себя через дочернюю таблицу
		$namesThisClass = get_class($this);
		return array(
			'uclass'=>array(self::BELONGS_TO, 'uClasses', 'uclass_id'),

			'links'=>array(self::MANY_MANY, $namesThisClass,'setcms_'.strtolower($namesThisClass).'_links(from_self_id, to_self_id)'),
		);
	}

	function beforeDelete() {
		$this->clearMTMLink('links', Yii::app()->appcms->config['sys_db_type_InnoDB']);
		return parent::beforeDelete();
	}
}
