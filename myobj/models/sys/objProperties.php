<?php
class objProperties extends AbsBaseModel
{
	public $name; //models.CharField(max_length=255)
	public $codename; //models.CharField(max_length=30,unique=True)
	public $description; //models.CharField(max_length=255,blank=True)
	public $myfield; //models.PositiveSmallIntegerField(choices=MYCONF.TYPES_MYFIELDS_CHOICES, default=1)
	public $minfield; //models.CharField(max_length=4,blank=True)
	public $maxfield; //models.CharField(max_length=4,blank=True)
	public $required; //models.BooleanField(blank=True)
	public $udefault=''; //models.CharField(max_length=255,blank=True)
	public $setcsv=''; //models.CharField(max_length=255,blank=True)
 
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	public function relations()
	{
		return array(
			'classes'=>array(self::MANY_MANY, 'uClasses', 'setcms_uclasses_objproperties(to_objproperties_id, from_uclasses_id)'),
		);
	}
	public function defaultRules()
	{
		return array(
			array('name, codename, myfield', 'required'),
			array('name, description, udefault', 'length', 'max'=>255),

			array('codename', 'length', 'max'=>30),
			array('codename', 'unique', 'on'=>'insert', 'attributeName'=>'codename', 'className'=>get_class($this),'caseSensitive' =>'false'),
			array('minfield, maxfield', 'length', 'max'=>4),

			array('description, minfield, maxfield, setcsv', 'default', 'value'=>''),

			array('required', 'boolean'),
			array('myfield, minfield, ,maxfield', 'numerical'),
			array('udefault', 'default', 'value'=>false),
			array('setcsv', 'default', 'value'=>''),
		);
	}
	public function beforeSave() {
		if(parent::beforeSave()!==false) {
			if(trim($this->setcsv)=='') {
				$arrconfcms = Yii::app()->appcms->config;
				if(array_key_exists($arrconfcms['TYPES_MYFIELDS_CHOICES'][$this->myfield],$arrconfcms['rulesvalidatedef'])) {
					$valdefsetcsv = $arrconfcms['rulesvalidatedef'][$arrconfcms['TYPES_MYFIELDS_CHOICES'][$this->myfield]];
					$this->setcsv = $valdefsetcsv;
				}
			}
			return true;
		}
		else return parent::beforeSave();
	}
	public function getTYPES_MYFIELDSOptions() {
		return Yii::app()->appcms->config['TYPES_MYFIELDS_CHOICES'];
	}
	public function defaultAttributeLabels() {
		return array(
			'name' => 'name',
			'codename' => 'code name',
			'myfield' => 'type',
			'minfield' => 'min',
			'maxfield' => 'max',
			'udefault' => 'default',
	   );
	}
	public function defaultElementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			),
			'codename'=>array(
				'type'=>'text',
			),
			'description'=>array(
				'type'=>'textarea',
			),
			'myfield'=>array(
				'type'=>'dropdownlist',
				'items'=>$this->getTYPES_MYFIELDSOptions(),
			),
			'minfield'=>array(
				'type'=>'text',
			),
			'maxfield'=>array(
				'type'=>'text',
			),
			'required'=>array(
				'type'=>'checkbox',
			),
			'udefault'=>array(
				'type'=>'text',
			),
			'setcsv'=>array(
				'type'=>'textarea',
			)
		);
	}
	public function beforeDelete() {
		//запрет на удаление отдельных объектов системы
		if(array_search($this->codename, Yii::app()->appcms->config['controlui']['none_del']['prop'])!==false) return false;

		return parent::beforeDelete();
	}
}

