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
			if(!isset($conf['paramsRules'])) continue;

			$objFormEArray = MYOBJ\appscms\core\base\form\DForm::create();
			foreach($conf['paramsRules'] as $nameParam => $rules) {
				$objFormEArray->addAttributeRule($nameParam, $rules);
			}
			$this->_formEArrayValid[$name] = $objFormEArray;
		}
		//
	}

	public function afterValidate() {
		parent::afterValidate();
	}

	private $onBeforeSave = false;
	protected function beforeSave() {
		if ((defined('YII_DEBUG') && YII_DEBUG) && $this->validate()==false && $this->getErrors()) {
			throw new CException(Yii::t('cms', 'this class errors: ' . print_r($this->getErrors(), true)));
		}

		if(!$this->onBeforeSave) {
			$this->onBeforeSave = true;
		}
		else {
			$this->onBeforeSave = false;
		}

		if(!parent::beforeSave()) return false;

		return true;
	}

	public function beforeValidate() {
		if(!parent::beforeValidate()) return false;

		//EArray
		$is_errors_earray = false;
		foreach($this->confEArray() as $nameParam => $conf) {
			if(!isset($conf['paramsRules'])) continue;

			$this->_formEArrayValid[$nameParam]->attributes = $this->getEArray($nameParam);
			if ($this->_formEArrayValid[$nameParam]->validate() == false) {
				$this->addErrors($this->_formEArrayValid[$nameParam]->getErrors());
				$is_errors_earray = true;
			}
		}
		if($is_errors_earray) return false;
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
		$conf = $this->confEArray();
		$conf = $conf[$name];

		if(!$val) return '';

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
	public function editEArray($name, $array, $is_merge=true) {
		if($is_merge) {
			$unserialize = $this->unserializeType($name);

			$unserialize = array_merge($unserialize, $array);

			foreach($unserialize as $k => $val) {
				if(!$val) {
					unset($unserialize[$k]);
				}
			}
		}
		else {
			$unserialize = $array;
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

	public $mode_compare_save = false;
	public function get_mode_compare_save_none_compare() {
		return array();
	}

	public $setAttributesNew = array();
	public function setAttributes($values,$safeOnly=true) {
		parent::setAttributes($values,$safeOnly);

		$this->setAttributesNew = $values;
	}
	public function setAttribute($name,$value) {
		$this->setAttributesNew[$name] = $value;

		return parent::setAttribute($name,$value);
	}

	public function save($runValidation=true,$attributes=null) {
		//get_mode_compare_save_none_compare
		$this->beforeSave();
		if($this->isNewRecord==false && !$attributes) {
			$attributes = array();

			if($this->mode_compare_save) {
				$old = $this->getOldAttributes();
				$noneCompare = $this->get_mode_compare_save_none_compare();
				foreach($this->getAttributes() as $k=>$v) {
					if(isset($noneCompare[$k]) || ($v!==null && $old[$k]!=$v)) {
						$attributes[] = $k;
					}
				}
			}
			elseif($this->setAttributesNew) {
				$attributes = array_keys($this->setAttributesNew);
				$this->setAttributesNew = array();
			}

			$names = $this->attributeNames();
			foreach($attributes as $k=>$name) {
				if(array_search($name, $names)===false) {
					unset($attributes[$k]);
				}
			}

			if(!$attributes) {
				$attributes=null;
			}
		}
		//
		return parent::save($runValidation, $attributes);
	}

	protected function afterSave() {
		parent::afterSave();

		$this->_old_attributes = $this->getAttributes();
	}

	public function afterSaveLinkEdit($type, $namerelation, $idsObj) {

	}
}
