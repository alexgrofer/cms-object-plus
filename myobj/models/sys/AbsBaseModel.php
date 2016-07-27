<?php
abstract class AbsBaseModel extends CActiveRecord
{
	public static function create($scenario='insert',$addParam1=null,$addParam2=null,$addParam3=null,$addParam4=null) {
		$newObj = new static($scenario);
		$newObj->afterCreate();
		return $newObj;
	}

	public function tableName()
	{
		return 'cmsplus_'.strtolower(get_class($this));
	}

	protected function afterFind() {
		parent::afterFind();
		$this->afterCreate();
	}

	public function primaryKey()
	{
		return 'id';
	}

	public static function model($className=null)
	{
		if(!$className) {
			$className = get_called_class();
		}
		return parent::model($className);
	}

	public function behaviors()
	{
		return array(
			'UserRelated'=>array(
				'class'=>'CmsRelatedBehavior',
			),
		);
	}
	public function setMTMcol($model,$array_elems,$array_value) {
		return $this->UserRelated->links_edit('edit',$model,$array_elems,$array_value);
	}

	public function clearMTMLinkNotInnoDB($nameRelation) {
		if(Yii::app()->appcms->config['sys_db_type_InnoDB']) {
			$this->UserRelated->links_edit('clear',$nameRelation);
		}
	}

	public $customAttributeLabels=array();
	protected function defaultAttributeLabels() {
		return array();
	}
	final public function attributeLabels() {
		return array_merge($this->defaultAttributeLabels(), $this->customAttributeLabels);
	}

	/**
	 * метод будет вызван до любого запроса к базе, как для обычных выборок(findAll и т.д) так и для count
	 */
	protected function beforeMyQuery() {

	}

	protected function beforeFind() {
		parent::beforeFind();

		$this->beforeMyQuery();
	}
	protected function beforeCount() {
		parent::beforeCount();

		$this->beforeMyQuery();
	}

	protected function afterDelete() {
		parent::afterDelete();

		if(Yii::app()->appcms->config['sys_db_type_InnoDB']) {
			foreach ($this->foreign_on_delete_cascade() as $nameRelation) {
				if (isset($this->metaData->relations[$nameRelation])) {
					$relation = $this->metaData->relations[$nameRelation];
					$className = $relation->className;
					$namePRKey = $className::model()->primaryKey();
					$criteria = new CDbCriteria();
					$criteria->addInCondition($namePRKey, \MYOBJ\appscms\core\base\SysUtils::arrvaluesmodel($this->$nameRelation(new CDbCriteria(array('select' => $namePRKey))), $namePRKey));
					$className::model()->deleteAll($criteria);
				}
			}
		}
		//удаление в дочерних таблицих
		foreach($this->foreign_on_delete_cascade_MTM() as $nameRelation) {
			if(isset($this->metaData->relations[$nameRelation])) {
				$this->clearMTMLinkNotInnoDB($nameRelation);
			}
		}
	}

	/**
	 * Удаление объекта если база не поддерживает внешние ключи
	 * @return array
	 */
	protected function foreign_on_delete_cascade() {
		return array();
	}

	/**
	 * Для удаления ссылок из дочерних таблиц
	 * @return array
	 */
	protected function foreign_on_delete_cascade_MTM() {
		return array();
	}

	/**
	 * Для RESTRICT ссылок
	 * @return array
	 */
	protected function foreign_on_restrict_cascade() {
		return array();
	}

	public function beforeDelete() {
		if(!parent::beforeDelete()) return false;

		//none_del_id
		$thisFindName = get_class($this);
		if(isset(Yii::app()->appcms->config['none_del_id'][$thisFindName])) {
			foreach(Yii::app()->appcms->config['none_del_id'][$thisFindName] as $primaryKey) {
				if($this->primaryKey == $primaryKey) return false;
			}
		}

		if(Yii::app()->appcms->config['sys_db_type_InnoDB']) {
			foreach ($this->foreign_on_restrict_cascade() as $nameRelation) {
				if (isset($this->metaData->relations[$nameRelation])) {
					if ($this->$nameRelation(new CDbCriteria(array('limit' => 1)))) {
						throw new CException('CMS problem restrict cascade relation' . $nameRelation);
					}
				}
			}
		}

		return true;
	}

	private $_old_attributes=array();
	public function getOldAttributes() {
		return $this->_old_attributes;
	}
	public function getOldAttribute($name) {
		return $this->_old_attributes[$name];
	}

	/**
	 * @var MYOBJ\appscms\core\base\form\DForm null
	 */
	private $_formEArrayValid=array();

	public function init() {
		parent::init();

		$this->_old_attributes = $this->attributes;
	}

	public function afterCreate() {
		$this->_old_attributes = $this->attributes;

		//EArray
		foreach($this->confEArray() as $name => $conf) {
			$objFormEArray = MYOBJ\appscms\core\base\form\DForm::create();
			foreach($conf['paramsRules'] as $nameParam => $rules) {
				$objFormEArray->addAttributeRule($nameParam, $rules);
			}
			$this->_formEArrayValid[$name] = $objFormEArray;
		}
		//
	}

	private $wasAfterValidated=false;
	public function afterValidate() {
		parent::afterValidate();

		$this->wasAfterValidated=true;
	}

	protected function beforeSave() {
		if(!parent::beforeSave()) return false;

		/**
		 * если юзер в коде даже не пытался вызвать $obj->validate() перед сохранением
		 */
		if (((defined('YII_DEBUG') && YII_DEBUG) || $this->wasAfterValidated == false) && $this->getErrors()) {
			throw new CException(Yii::t('cms', 'this class errors: ' . print_r($this->getErrors(), true)));
		}

		return true;
	}

	public function beforeValidate() {
		if(!parent::beforeValidate()) return false;

		//EArray
		foreach($this->confEArray() as $nameParam => $v) {
			$this->_formEArrayValid[$nameParam]->attributes = $this->getEArray($nameParam);
			if ($this->_formEArrayValid[$nameParam]->validate() == false) {
				$this->addErrors($this->_formEArrayValid[$nameParam]->getErrors());
				return false;
			}
		}
		//

		return true;
	}

	public function confEArray() {
		return array();
	}

	private function unserializeType($name) {
		$conf = $this->confEArray(); $conf = $conf[$name];

		if(!$this->$name) return array();

		if($conf['type']=='serialize') {
			return unserialize($this->$name);
		}
		elseif($conf['type']=='json') {
			return CJSON::decode($this->$name);
		}
	}
	private function serializeType($name, $val) {
		$conf = $this->confEArray(); $conf = $conf[$name];

		if($this->$name==='') return '';

		if($conf['type']=='serialize') {
			return serialize($val);
		}
		elseif($conf['type']=='json') {
			return CJSON::encode($val);
		}
	}

	/**
	 * @param $name
	 * @param $array = array('param_earray_1'=>1), $array val=>'' ключ будет удален из массива
	 */
	public function editEArray($name, $array) {
		$unserialize = $this->unserializeType($name);

		$unserialize = array_merge($unserialize, $array);

		foreach($unserialize as $k => $val) {
			if(trim($val)==='' || $val===null || $val===false) {
				unset($unserialize[$k]);
			}
		}

		$this->$name = $this->serializeType($name, $unserialize);
	}

	/**
	 * @return array
	 */
	public function getEArray($name, $name_param=null) {
		$unserialize = $this->unserializeType($name);

		if($name_param===null) {
			return $unserialize;
		}

		if(!isset($unserialize[$name_param])) {
			return null;
		}

		return $unserialize[$name_param];
	}

	/**
	 * Описывает элементы формы для объекта
	 * @return array
	 */
	public function elementsForm() {
		return array();
	}

	public $isUpdateAttributesChanged = false;
	public function update($attributes=null) {
		if(!$attributes && $this->isUpdateAttributesChanged) {
			$attributesEdit = array();
			foreach($this->isUpdateAttributesChanged as $attribute) {
				if($this->$attribute != $this->getOldAttribute($attribute)) {
					$attributesEdit[] = $attribute;
				}
			}
			if($attributesEdit) {
				$attributes = $attributesEdit;
			}
		}

		return parent::update($attributes);
	}
}
