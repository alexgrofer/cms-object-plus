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
	public function getMTMcol($model,$pkelem,$exp_select) {
		return $this->UserRelated->links_edit('select',$model,array($pkelem),$exp_select);
	}
	public function setMTMcol($model,$array_elems,$array_value) {
		return $this->UserRelated->links_edit('edit',$model,$array_elems,$array_value);
	}
	public function clearMTMLink($nameRelation, $isDELETE_CASCADE) { //использовать только для MTM, для HAS_MANY нет смысла так как поля не удаляются а апдейтятся
		if($isDELETE_CASCADE==false) {
			$this->UserRelated->links_edit('clear',$nameRelation);
		}
	}

	protected $elements_enable_class = array();
	/**
	 * Управляемое добавление новых свойств
	 * Что угодно в модель поставить нельзя, только умышленно через этот метод!
	 * если уже есть такое свойство класса будет исключение
	 * @param $name
	 * @param $value
	 */
	public function addElemClass($name, $defValue=null) {
		if(!$this->isAddElemClass($name)) {
			$this->elements_enable_class[] = $name;
			$this->$name = $defValue;
		}
		else {
			throw new CException(Yii::t('cms','element "{prop}" is already add class  "{class}"',
				array('{prop}'=>$name, '{class}'=>get_class($this))));
		}
	}

	/**
	 * Проверка на существование кастомных свойств в этом экземпляре
	 * @param $name
	 * @return bool
	 */
	public function isAddElemClass($name) {
		return in_array($name,$this->elements_enable_class);
	}
	public function setAttributes($values, $safeOnly=true) {
		if(is_array($values) && count($values)) {
			//SWITCH MY TYPES
			foreach($values as $nameElem => $val) {
				//CASE type EAarray
				if(($pos = strpos($nameElem,'earray_'))!==false) {
					$arrName = explode('__',substr($nameElem,0,$pos));
					$index = (isset($arrName[2])?$arrName[2]:null);
					$this->edit_EArray($val,$arrName[0],$arrName[1],$index);
				}
				//CASE type new type
				//if(...)
			}
		}

		parent::setAttributes($values, $safeOnly);
	}
	public function __set($name, $value) {
		//init new elements
		if(in_array($name,$this->elements_enable_class)) {
			$this->$name =  $value;
		}
		parent::__set($name, $value);
	}

	protected function beforeSave() {
		if(parent::beforeSave()!==false) {
			//сбрасываем ключи для того что бы ключи всегда шли по порядку, т.к возможно удаление элемента массива
			$typesEArray = $this->typesEArray();
			if(count($typesEArray)) {
				foreach($typesEArray as $nameCol => $setting) {
					$valuetypesEArray = $this->get_EArray($nameCol);
					if(count($valuetypesEArray) && $setting['conf']['isMany']) {
						foreach($valuetypesEArray as $index => $array) {
							foreach($array as $nameElem =>$val) {
								$nameElemClass = $nameCol.'__'.$nameElem.'__'.$index.'earray_';
								$isUnsafe = true;
								foreach($this->rules() as $arrayRule) {
									if($arrayRule[0]==$nameElemClass && $arrayRule[1]!='unsafe') {
										$isUnsafe = false;
										break;
									}
								}
								if($isUnsafe) {
									unset($valuetypesEArray[$index][$nameElem]);
								}
							}
						}
						$this->$nameCol = serialize(array_values($valuetypesEArray));
					}
				}
			}
			return true;
		}
		else return parent::beforeSave();
	}

	/**
	 * @var array Массив всегда хранит сохраненный элементы типа earray даже если нет правил или оно unsafe
	 */
	protected $save_tmp_not_rules_earray = array();

	public function edit_EArray($value,$nameCol,$nameElem,$index=null) {
		$isExists = $this->has_EArray($nameCol,$nameElem,$index)?true:false;
		$indexStr = ($index!==null)?'__'.$index:'';
		$nameElemClass = $nameCol.'__'.$nameElem.$indexStr.'earray_';
		if(!$this->isAddElemClass($nameElemClass)) { //что то придумать для правильной логики добавления
			//создаем элемент
			$this->addElemClass($nameElemClass,$value);
		}
		else {
			$this->$nameElemClass = $value;
		}

		$unserializeArray = $this->get_EArray($nameCol);

		if($index!==null) {
			if($isExists && !trim($value)) { //пустые не храним в базе
				unset($unserializeArray[$index][$nameElem]);
				if(!count($unserializeArray[$index])) {
					unset($unserializeArray[$index]);
				}
			}
			elseif(trim($value)) { //если новый
				if(!isset($unserializeArray[$index])) {
					$unserializeArray[$index] = array();
				}
				$unserializeArray[$index][$nameElem] = $value;
			}
		}
		else {
			if($isExists && !trim($value)) {
				unset($unserializeArray[$nameElem]); //пустые не храним в базе
			}
			elseif($value) {
				$unserializeArray[$nameElem] = $value;
			}
		}

		if(count($unserializeArray)) {
			$this->$nameCol = serialize($unserializeArray);
			$this->save_tmp_not_rules_earray[$nameCol] = $unserializeArray;
		}
		else {
			$this->$nameCol = null;
		}

		if($index!==null) {
			//для новых элементов нужно прописывать правила
			if(trim($value) && !count($this->get_EArray($nameCol,null,$index,true))) {
				$this->genetate_rule_EArray($nameCol,$nameElem,$index);
			}

			//если полностью удалил элементы убераем все правила так как считаем что элемент не нужен
			elseif(!trim($value) && !count($this->get_EArray($nameCol,null,$index))) {
				foreach($this->customRules as $k => $arrayRule) {
					if($arrayRule[0]==$nameElemClass) {
						unset($this->customRules[$k]);
					}
				}
			}
		}
	}

	/**
	 * Возвращает значение
	 * @param $nameCol
	 * @param null $nameElem
	 * @param null $index
	 * @param bool $isOld
	 * @return array|mixed
	 */
	public function get_EArray($nameCol,$nameElem=null,$index=null,$isOld=false) {
		$strArr = (!$isOld)?$this->attributes[$nameCol]:$this->old_attributes[$nameCol];
		$elem = array();
		if(trim($strArr) && ($unserializeArray = @unserialize($strArr))) {
			if($nameElem) { //по ключу элемента или в зависимости от индекса при множественном
				$elem = ($index!==null)?$unserializeArray[$index][$nameElem]:$unserializeArray[$nameElem];
			}
			else{
				$elem = $unserializeArray;
				if($index!==null) {
					$elem = (isset($unserializeArray[$index]))?$unserializeArray[$index]:array();
				}
			}
		}
		return $elem;
	}

	/**
	 * Проверяет существует сам элемент
	 * @param $nameCol
	 * @param $nameElem
	 * @param null $index
	 * @return bool
	 */
	public function has_EArray($nameCol,$nameElem,$index=null,$isOld=false) {
		$strArr = (!$isOld)?$this->attributes[$nameCol]:$this->old_attributes[$nameCol];
		$result = false;
		if(trim($strArr) && ($unserializeArray = @unserialize($strArr))) {
			if($index!==null && isset($unserializeArray[$index][$nameElem])) $result = true;
			elseif($index===null && isset($unserializeArray[$nameElem])) $result = true;
		}
		return $result;
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

	public function genetate_form_EArray($nameCol,$nameE,$index=null) {
		$index = ($index!==null)?'__'.$index:'';
		$nameElemClass = $nameCol.'__'.$nameE.$index.'earray_';
		$typesEArray = $this->typesEArray();
		$elemRuleConf = '*';
		if(isset($typesEArray[$nameCol]['elementsForm'][$nameE])) {
			$elemRuleConf = $nameE;
		}
		$this->customElementsForm[$nameElemClass] = $typesEArray[$nameCol]['elementsForm'][$elemRuleConf];
	}
	public function genetate_rule_EArray($nameCol,$nameE,$index=null) {
		$index = ($index!==null)?'__'.$index:'';
		$nameElemClass = $nameCol.'__'.$nameE.$index.'earray_';
		$typesEArray = $this->typesEArray();
		$elemRuleConf = '*';
		if(isset($typesEArray[$nameCol]['rules'][$nameE])) {
			$elemRuleConf = $nameE;
		}
		foreach($typesEArray[$nameCol]['rules'][$elemRuleConf] as $settingArray) {
			array_unshift($settingArray,$nameElemClass);
			$this->customRules[] = $settingArray;
		}
	}

	public function typesEArray() {
		return array();
	}

	/**
	 * Если собираемся объявить новый объект нужно вызывать в ручную $newObj = $class();$newObj->declareObj();
	 */
	public function declareObj() {
		//обявление элемента

		foreach($this->attributes as $k => $v) {
			$this->old_attributes[$k] = $v;
		}

		//types

		//EArray
		$typesEArray = $this->typesEArray();

		if(count($typesEArray)) {
			foreach($typesEArray as $nameCol => $setting) {
				$valuetypesEArray = $this->get_EArray($nameCol);
				if(isset($setting['elements']) && count($setting['elements'])) {
					if(count($valuetypesEArray) && $setting['conf']['isMany']) {
						foreach($valuetypesEArray as $index => $valuetypesEArrayElem) {
							foreach($setting['elements'] as $nameE) {
								$getValElem = (isset($valuetypesEArrayElem[$nameE]))?$valuetypesEArrayElem[$nameE]:null;
								$this->edit_EArray($getValElem,$nameCol,$nameE,$index);
								$this->genetate_form_EArray($nameCol,$nameE,$index);
								$this->genetate_rule_EArray($nameCol,$nameE,$index);
							}
						}
					}
					elseif($setting['conf']['isMany']==false) {
						foreach($setting['elements'] as $nameE) {
							$getValElem = (count($valuetypesEArray) && isset($valuetypesEArray[$nameE]))?$valuetypesEArray[$nameE]:null;
							$this->edit_EArray($getValElem,$nameCol,$nameE);
							$this->genetate_form_EArray($nameCol,$nameE);
							$this->genetate_rule_EArray($nameCol,$nameE);
						}
					}
					//если он пустой !count($valuetypesEArray) то сгенерить только для не множественного так как элемент нужен для формы
				}
			}
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
}
