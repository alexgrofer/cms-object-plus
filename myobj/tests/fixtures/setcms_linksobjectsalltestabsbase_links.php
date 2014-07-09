<?php
abstract class AbsBaseLinksObjects extends AbsBaseModel
{
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

	public $idobj;

	public function relations()
	{
		$namestrthisclass = get_class($this);
		return array(
			'links'=>array(self::MANY_MANY, $namestrthisclass,'setcms_'.strtolower($namestrthisclass).'_links(from_self_id, to_self_id)'), // links = models.ManyToManyField("self",blank=True)
		);
	}

	function beforeDelete() {
		$this->clearMTMLink('links', Yii::app()->appcms->config['sys_db_type_InnoDB']);
		return parent::beforeDelete();
	}
}
