<?php
abstract class AbsBaseModel extends CActiveRecord
{
	public function primaryKey()
	{
		return 'id';
	}

	public $old_attributes=array();
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

	private $_isValidated;
	public function beforeValidate() {
		if(parent::beforeValidate()) {
			$this->_isValidated = true;
		}
		else return false;
	}

	protected function beforeSave() {
		if($beforeSave = parent::beforeSave()) {
			if($this->_isValidated==fales && $this->validate()==false) {
				throw new CException(
					Yii::t('cms','this class errors: '.print_r($this->getErrors(),true))
				);
			}

			return true;
		}

		return $beforeSave;
	}

	public $customRules=array();
	protected function defaultRules() {
		return array();
	}

	/**
	 * Больше нельзя наследовать rules из этого класса, теперь используем метод defaultRules для задания правил
	 * если необходимо динамически ДОБАВИТЬ правила используем свойство customRules
	 * @return array
	 */
	final public function rules() {
		return array_merge($this->defaultRules(), $this->customRules);
	}

	public $customElementsForm=array();
	protected function defaultElementsForm() {
		return array();
	}
	final public function elementsForm() {
		return array_merge($this->defaultElementsForm(), $this->customElementsForm);
	}

	public $customAttributeLabels=array();
	protected function defaultAttributeLabels() {
		return array();
	}
	final public function attributeLabels() {
		return array_merge($this->defaultAttributeLabels(), $this->customAttributeLabels);
	}

	/**
	 * Если собираемся объявить новый объект нужно вызывать в ручную $newObj = $class();$newObj->declareObj();
	 */
	public function declareObj() {
		//обявление элемента

		foreach($this->attributes as $k => $v) {
			$this->old_attributes[$k] = $v;
		}
	}
	public function initObj() {
		//инициализация элемента элемента
	}

	protected function afterFind() {
		parent::afterFind();

		$this->declareObj();
		$this->initObj();
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
}
