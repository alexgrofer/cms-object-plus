<?php
abstract class AbsModel extends CActiveRecord
{
	public function primaryKey()
	{
		return 'id';
	}
	public $isHeaderModel=false;
	public $old_attributes=array();
	public static function model($className=null)
	{
		if($className===null) {
			$class = new static();
			$className = get_class($class);
		}
		return parent::model($className);
	}

	private $_conditStart=array();
	public function setuiprop($array,$save_dbCriteria=null,$isclear=false) {
		// $array=array('condition'=>array(array('p1','<=','23', 'AND'), array('p2', true, 'IN(','1,2,3)', 'OR')),'select'=>array('*' | ['p1','p2','p3']),'order'=>array(array('p1','desc')[,array('p1')]?))
		if($save_dbCriteria===null) {
			$save_dbCriteria = $this->dbCriteria;
		}
		// олько новые условия - сетреть все предыдущие условия
		if($isclear && count($this->_conditStart)) {
			foreach(count($this->_conditStart) as $str_cond) {
				$save_dbCriteria->condition = str_replace($str_cond,'',$save_dbCriteria->condition);
			}
		}
		//для обычных моделей свойтва не трубуются
		if($this->isHeaderModel) $properties = $this->getClassProperties(); //task проверить тут были изменения теперь только свойства этого объекта выбираются раньше выбиральсь все
		$arrconfcms = Yii::app()->appcms->config;
		if(array_key_exists('condition',$array)) {
			$propYes = false;
			foreach($array['condition'] as $cond) {
				$typecond = (count($cond)<5)?'AND':$cond[4];
				//prop
				$isand = ((!count($this->_conditStart) && $save_dbCriteria->condition)?' AND ':'');
				if($cond[1]==true) {
					$propYes = true;
					if(!isset($properties[$cond[0]])) {
						throw new CException(Yii::t('cms','None prop "{prop}" object class  "{class}"',
							array('{prop}'=>$cond[0], '{class}'=>$this->uclass->codename)));
					}
					$critStr = $isand."(lines_find.".$arrconfcms['TYPES_COLUMNS'][$properties[$cond[0]]->myfield]." ".$cond[2]." ".$cond[3]." AND (lines_find.property_id=".$properties[$cond[0]]->id.")) ".$typecond." ";
					//ждойнить нужно каждый раз тут
				}
				//param
				else {
					$critStr = ((trim($save_dbCriteria->condition)=='' || (stripos($save_dbCriteria->condition,'and')!==false || stripos($save_dbCriteria->condition,'or')!==false))?'':' AND ').$cond[0].'  '.$cond[2].'  '.$cond[3].'  '.$typecond.' ';
				}
				$save_dbCriteria->condition .= $critStr;
				$this->_conditStart[] = $critStr;
			}
			if($propYes!='') {
				//нужно джойнить таблицу что бы в ней появился столбец по которому можно будет отсортировать
				$save_dbCriteria->with['lines_find']['together'] = true;
				//в целях оптимизации нам не нужны в селекте никакие лишнии данные,оставим только property_id так как должен быть хоть один столбец с синтаксисе sql
				$save_dbCriteria->with['lines_find']['select'] = 'property_id';
			}
		}
		if(array_key_exists('order',$array) && count($array['order'])) {
			//если свойство в массив добавляется 3 элемент true
			$elem_order = $array['order'][0];
			$isprop = ((count($elem_order)==3 && $elem_order[2]==true)?true:false);
			$typf = (count($elem_order)>=2)?$elem_order[1]:'asc';

			if($isprop) { //is prop
				if(!isset($properties[$elem_order[0]])) {
					throw new CException(Yii::t('cms','None prop "{prop}" object class  "{class}"',
						array('{prop}'=>$elem_order[0], '{class}'=>$this->uclass->codename)));
				}

				$typeprop = $arrconfcms['TYPES_COLUMNS'][$properties[$elem_order[0]]->myfield];
				$textsql = '(case when lines_sort.'.$typeprop.' is null then 1 else 0 end) asc, lines_sort.'.$typeprop.' '.$typf;
				//нужно джойнить таблицу что бы в ней появился столбец по которому можно будет отсортировать
				$save_dbCriteria->with['lines_sort']['together'] = true;
				//в целях оптимизации нам не нужны в селекте никакие лишнии данные,оставим только property_id так как должен быть хоть один столбец с синтаксисе sql
				$save_dbCriteria->with['lines_sort']['select'] = 'property_id';
				//сама сортировка
				$save_dbCriteria->with['lines_sort']['order'] = $textsql;
				//для того что бы не попали лишнии строки(проблемы limit) при джойне ограничим только нужным свойством которое учавствует в сортировке
				$save_dbCriteria->with['lines_sort']['condition'] = 'lines_sort.property_id='.$properties[$elem_order[0]]->id;
			}
			//is param
			else {
				$textsql = $elem_order[0].' '.$typf;
				$save_dbCriteria->order = $textsql;
			}
		}
		if(array_key_exists('limit',$array) && count($array['limit'])) {
			//всегда группировать так как и поиск и сортировка создают строки в результате запроса
			if(isset($save_dbCriteria->with['lines_sort']) || isset($save_dbCriteria->with['lines_find'])) {
				$save_dbCriteria->group = 't.id';
			}
			$save_dbCriteria->limit = $array['limit']['limit'];
			$save_dbCriteria->offset = $array['limit']['offset'];
		}

		$this->setDbCriteria($save_dbCriteria);
		return $this;
	}

	/**
	 * @var bool Мержить запрос с дополнительными данными, необходимо если:
	 * (FALSE) мы не будем в списке использовать свойства каким либо образом
	 */
	public $force_join_props = true;

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
		if(!in_array($name,$this->elements_enable_class)) {
			$this->elements_enable_class[] = $name;
			$this->$name = $defValue;
		}
		else {
			throw new CException(Yii::t('cms','element "{prop}" is already add class  "{class}"',
				array('{prop}'=>$name, '{class}'=>get_class($this))));
		}
	}
	public function setAttributes($values) {
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

		parent::setAttributes($values);
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
		if(!property_exists($this,$nameElemClass)) { //что то придумать для правильной логики добавления
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
	 * @param $name
	 * @param null $nameElem
	 * @param null $index
	 * @return mixed может вернуть как массив так и значение string
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
	public function has_EArray($nameCol,$nameElem,$index=null) {
		$result = false;
		if(trim($this->$nameCol) && ($unserializeArray = @unserialize($this->$nameCol))) {
			if($index!==null && isset($unserializeArray[$index][$nameElem]))  $result = true;
			elseif($index==null && isset($unserializeArray[$nameElem])) $result = true;
		}
		return $result;
	}

	public $customRules=array();
	public function customRules() {
		return array();
	}
	public function rules() {
		$defCustomRules = $this->customRules();
		return array_merge($defCustomRules, $this->customRules);
	}

	public $customElementsForm=array();
	public function customElementsForm() {
		return array();
	}
	public function elementsForm() {
		$defCustomElementsForm = $this->customElementsForm();
		return array_merge($defCustomElementsForm, $this->customElementsForm);
	}

	public $customAttributeLabels=array();
	public function customAttributeLabels() {
		return array();
	}
	public function attributeLabels() {
		$defCustomAttributeLabels = $this->customAttributeLabels();
		return array_merge($defCustomAttributeLabels, $this->customAttributeLabels);
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
		if(isset($typesEArray[$nameCol]['rules'][$elemRuleConf])) {
			foreach($typesEArray[$nameCol]['rules'][$elemRuleConf] as $settingArray) {
				array_unshift($settingArray,$nameElemClass);
				$this->customRules[] = $settingArray;
			}
		}
	}

	protected function dinamicModel() {
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
	public function typesEArray() {
		return array();
	}

	/**
	 * Если собираемся объявить новый объект нужно вызывать в ручную $newObj = $class::model()->declareObj()
	 */
	public function declareObj() {
		//обявление элемента элемента
	}
	protected function initObj() {
		//инициализация элемента элемента
	}

	protected function afterFind() {
		parent::beforeFind();
		$this->declareObj();
		$this->initObj();
	}
}
