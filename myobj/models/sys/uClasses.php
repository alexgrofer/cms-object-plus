<?php
class uClasses extends AbsBaseModel
{
	public $name; //models.CharField(max_length=255)
	public $codename; //models.CharField(max_length=30,unique=True)
	public $description; //models.CharField(max_length=255,blank=True)
	public $tablespace; //models.PositiveSmallIntegerField(choices=MYCONF.MYSPACE_TABLES_CHOICES, default=1)

	public function relations()
	{
		return array(
			'properties'=>array(self::MANY_MANY, 'objProperties', 'cmsplus_uclasses_objproperties(from_uclasses_id, to_objproperties_id)'),
			'association'=>array(self::MANY_MANY, 'uClasses', 'cmsplus_uclasses_association(from_uclasses_id, to_uclasses_id)'),
			'association_back'=>array(self::MANY_MANY, 'uClasses', 'cmsplus_uclasses_association(to_uclasses_id, from_uclasses_id)'),
			//'objectCount'=>array(self::STAT, 'myObjHeaders', 'uclass_id'),
		);
	}
	public function rules()
	{
		return array(
			array('name,  codename', 'required'),
			array('name', 'length', 'max'=>255),

			array('codename', 'match', 'not' => true, 'pattern' => '/\s+/'),
			array('codename', 'unique', 'attributeName'=>'codename', 'className'=>get_class($this), 'allowEmpty'=>false, 'allowEmpty'=>false),

			array('tablespace', 'default', 'value'=>1),
			array('description', 'default', 'value'=>''),
			array('tablespace', 'numerical'),

		);
	}
	public function getobjectCount() {
		return $this->objects()->count();
	}

	public static function getTSPACESOptions(){
		$oprion = array();
		foreach(Yii::app()->appcms->config['spacescl'] as $key => $value) {
			$oprion[$key] = $value['namemodel'];
		}
		return $oprion;
	}
	protected function defaultAttributeLabels() {
		return array(
			'name' => 'name',
			'codename' => 'code name',
			'description' => 'description',
			'tablespace' => 'table space',
	   );
	}
	public function elementsForm() {
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
			'tablespace'=>array(
				'type'=>'dropdownlist',
				'items'=>self::getTSPACESOptions(),
			),
		);
	}
	//user func Object class
	/**
	 * Инициализирует новый объект header от класса
	 * @return CActiveRecord
	 */
	public function initobject() {
		$nameModelHeaders = $this->getNameModelHeaderClass();
		$newobj = $nameModelHeaders::create('insert',$this->primaryKey);
		return $newobj;
	}

	/**
	 * Название модели заголовка этого класса
	 * @return string
	 */
	public function getNameModelHeaderClass() {
		$nameModelHeaders = Yii::app()->appcms->config['spacescl'][$this->tablespace]['namemodel'];
		return $nameModelHeaders;
	}

	/**
	 * Список типов ссылок этого класса
	 * @return array
	 */
	public function getNamesModelLinks(){
		return Yii::app()->appcms->config['spacescl'][$this->tablespace]['nameModelLinks'];
	}

	/**
	 * Вытаскивает ссылку на объект класса с установленной критерией для поиска объектов
	 * @return CActiveRecord
	 */
	public function objects() { //namesvprop left join props lines
		$nameClass = $this->getNameModelHeaderClass();
		$modelheaders = $nameClass::model();
		if(!$modelheaders->is_independent) {
			$modelheaders->dbCriteria->compare($modelheaders->getTableAlias() . '.uclass_id', $this->primaryKey);
		}
		return $modelheaders;
	}

	static function getclass($class_Id_Or_CodeName) {
		$param = preg_match('/^[0-9]+$/', $class_Id_Or_CodeName)?'id':'codename';
		$objClass = uClasses::model()->findByAttributes(array($param => $class_Id_Or_CodeName));
		return $objClass;
	}

	public function hasAssotiation($codeName, $is_back=false) {
		$criteria = new CDbCriteria();
		$ass_type = $is_back==false?'association':'association_back';
		return (bool) $this->$ass_type($criteria->compare($ass_type.'.codename', $codeName));
	}

	protected function foreign_on_delete_cascade_MTM() {
		return array(
			'properties',
			'association',
		);
	}
}

