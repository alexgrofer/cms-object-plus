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
		if($this->isHeaderModel) $properties = $this->get_properties(); //task проверить тут были изменения теперь только свойства этого объекта выбираются раньше выбиральсь все
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
					$critStr = $isand."(lines.".$arrconfcms['TYPES_COLUMNS'][$properties[$cond[0]]->myfield]." ".$cond[2]." ".$cond[3]." AND property_alias.codename='".$cond[0]."') ".$typecond." ";
				}
				//param
				else {
					$critStr = ((trim($save_dbCriteria->condition)=='' || (stripos($save_dbCriteria->condition,'and')!==false || stripos($save_dbCriteria->condition,'or')!==false))?'':' AND ').$cond[0].'  '.$cond[2].'  '.$cond[3].'  '.$typecond.' ';
				}
				$save_dbCriteria->condition .= $critStr;
				$this->_conditStart[] = $critStr;
			}
			if($propYes!='') {
				$save_dbCriteria->with['lines'] = array('with' => 'property_alias', 'together'=>true);
				$save_dbCriteria->with['uclass.properties'] = array();
			}
		}
		if(array_key_exists('order',$array) && count($array['order'])) {
			//если свойство в массив добавляется 3 элемент true
			$elem_order = $array['order'][0];
			$isprop = ((count($elem_order)==3 && $elem_order[2]==true)?true:false);
			$typf = (count($elem_order)>=2)?$elem_order[1]:'asc';

			if($isprop) { //is prop
				$save_dbCriteria->with['lines_alias'] = array();
				if(!isset($properties[$elem_order[0]])) {
					throw new CException(Yii::t('cms','None prop "{prop}" object class  "{class}"',
						array('{prop}'=>$elem_order[0], '{class}'=>$this->uclass->codename)));
				}
				$save_dbCriteria->with['lines_order'] = array('on'=>"lines_order.property_id=".$properties[$elem_order[0]]->id,'together'=>true);

				$typeprop = $arrconfcms['TYPES_COLUMNS'][$properties[$elem_order[0]]->myfield];
				$textsql = '(case when lines_order.'.$typeprop.' is null then 1 else 0 end) asc, lines_order.'.$typeprop.' '.$typf;
				$save_dbCriteria->order = $textsql;
				//необходимо при пагинации что бы не создавались одинаковые элементы
				$save_dbCriteria->addCondition('lines_order.id IS NOT NULL');
			}
			//is param
			else {
				$textsql = $elem_order[0].' '.$typf;
				$save_dbCriteria->order = $textsql;
			}
		}
		if(array_key_exists('limit',$array) && count($array['limit'])) {
			$save_dbCriteria->group = $this->tableAlias.'.id';
			$save_dbCriteria->limit = $array['limit']['limit'];
			$save_dbCriteria->offset = $array['limit']['offset'];;
		}
		$this->setDbCriteria($save_dbCriteria);
		return $this;
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

	public function init() {
		parent::init();
		//запомнить старые значения иногда это требуется
		foreach($this->attributes as $key => $value) {
			$this->old_attributes[$key] = $value;
		}
	}

	protected $_validPropElements = array();
	/**
	 * Управляемое добавление новых свойств
	 * Что угодно в модель поставить нельзя, только умышленно через этот метод!
	 * @param $name
	 * @param $value
	 */
	public function addElemClass($name, $defValue=null) {
		if(!in_array($name,$this->_validPropElements)) {
			$this->_validPropElements[] = $name;
		}
		self::__set($name, $defValue);
	}
	public function __set($name, $value) {
		if(in_array($name,$this->_validPropElements)) {
			$this->$name =  $value;
		}
		//case
		elseif(($pos = strpos($name,'earray_'))!==false) {
			//разбить по два подчеркивания
			$this->edit_EArray($value,'namecol','elem',0);
		}
		//elseif и т.д можно добавить еще свои типы

		parent::__set($name, $value);
	}

	public function edit_EArray($value,$colName,$nameElem,$index=null) {
		//добавить значение к псевдо элементу
		$this->addElemClass($colName.'__'.$colName.($index?'__'.$index:'').'earray_', $value);
		//добавить значение в нормальному элементу серриализации
		$unserializeArray = $this->get_EArray($colName);
		if(!$unserializeArray) {
			$unserializeArray=array();
		}
		if($index) {
			if(!isset($unserializeArray[$index])) {
				$unserializeArray[$index] = array();
			}
			$unserializeArray[$index][$nameElem] = $value;
		}
		else {
			$unserializeArray[$nameElem] = $value;
		}
		$this->$colName = serialize($unserializeArray);
	}

	/**
	 * Возвращает значение
	 * @param $name
	 * @param null $nameElem
	 * @param null $index
	 * @return mixed может вернуть как массив так и значение string
	 */
	public function get_EArray($nameCol,$nameElem=null,$index=null) {
		$elem = null;
		if(trim($this->$nameCol) && ($unserializeArray = @unserialize($this->$nameCol))) {
			if($nameElem) {
				$elem = ($index)?$unserializeArray[$index][$nameElem]:$unserializeArray[$nameElem];
			}
			else {
				$elem = $unserializeArray;
			}
		}
		return $elem;

		//добавить значение к псевдо элементу
		//добавить значение в нормальному элементу серриализации
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

	protected function dinamicModel() {
		$typesEArray = $this->typesEArray();

		if(count($typesEArray)) {
			foreach($typesEArray as $nameCol => $setting) {
				if(isset($setting['elements']) && count($setting['elements'])) {
					if(isset($setting['elements']) && count($setting['elements'])) {
						if(isset($setting['conf']['isMany']) && $setting['conf']['isMany']==true) {
							$valuetypesEArray = $this->get_EArray($nameCol);
							if(count($valuetypesEArray)) {
								foreach($valuetypesEArray as $kP => $arrP) {
									foreach($arrP as $kE => $vP) {
										$this->edit_EArray($vP,$nameCol,$kE,$kP);
									}
								}
							}
							//если он пустой просто инициализируем
							else {
								foreach($setting['elements'] as $nameE) {
									$this->edit_EArray('',$nameCol,$nameE);
								}
							}
						}
					}
				}
			}
			//генерим элементы
			//ненерим rules
			//генерим форму
		}
	}

	/**
	 * Возможность хранить массивы в базе.
	 * name_col_typeEArray_firstname
	 * @return array
	 */
	/*
	public function typesEArray() {
		return array(
			'name_col_typeEArray' => array(
				'elements' => array( //возможные ключи массива, если пусто ТО возможно добавление любых ключей
					'firstname',
					'lastname',
				),
				'conf' => array(
					'isMany'=>false, //множественное добавление вложенного массива
					'rules'=>array(
						array('firstname','required'),
						array('*','boolean'), // * для любых ключей
					),
				),
				'elementsForm' => array(
					'firstname'=>array(
						'type'=>'text',
					),
					'lastname'=>array(
						'type'=>'checkbox',
					),
				),
			)
		);
	}
	*/
	public function typesEArray() {
		return array();
	}
}
