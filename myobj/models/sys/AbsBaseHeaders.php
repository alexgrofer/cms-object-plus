<?php
abstract class AbsBaseHeaders extends AbsBaseModel
{
	public static function create($scenario='insert',$idClass=null,$addParam2=null,$addParam3=null,$addParam4=null) {
		$newObj = new static($scenario);
		if(!$newObj->is_independent) {
			$newObj->uclass_id = $idClass;
		}
		$newObj->afterCreate();
		return $newObj;
	}


	const PRE_LINKS='links_';
	const PRE_LINKS_BACK='links_back_';
	const PRE_LINKS_MTM='links_mtm_';
	const PRE_LINKS_MTM_BACK='links_mtm_back_';
	const NAME_TYPE_LINK_BASE='base';

	/**
	 * @var bool - true у текущего галоловка своя таблица
	 * false - лежит в общей таблице
	 */
	public $is_independent=false;
	/**
	 * @var int Переменная определяет текущий класс объекта используется если заголовок хранится в своей собственной таблице
	 */
	public $uclass_id;

	public function tableName()
	{
		return 'cmsplus_'.strtolower(get_class($this));
	}

	/**
	 * @var bool В запросе будет join нить таблицы при работе со свойствами.
	 * если false и свойства используются тогда будет запрос при каждом вызов списка свойств
	 * false - лучше использовать когда мы точно не собираемся вызывать свойства на данном этапе выборки
	 */
	public $force_join_props = true;

	/**
	 * @var bool
	 * Данные Свойств лежат в объектах модели AbsBaseLines, если мы не собираемся использовать свойства то нужно поставить этот параметр false
	 */
	public $isitlines = true;

	public function relations() {
		$relations = [];
		if($this->is_independent) {
			$relations['uclass'] = array(self::BELONGS_TO, 'uClasses',  '', 'on'=> 'uclass.id='.$this->uclass_id);
		}
		else {
			$relations['uclass'] = array(self::BELONGS_TO, 'uClasses', 'uclass_id');
		}

		if($this->isitlines == true) {
			$relations['lines'] = array(self::HAS_MANY, 'lines'.get_class($this), 'header_id');
		}
		return $relations;
	}

	public function rules() {
		return array(
			array('uclass_id', 'unsafe'),
		);
	}

	protected function beforeFind() {
		parent::beforeFind();

		$this->getDbCriteria()->with['uclass'] = [];
		//это нужно только при поиске для count есть beforeCount или beforeMyQuery(происходит до любого запроса) НО эти поля нам там не нужны
		/*
		 * в случае если есть поддержка свойств(isitlines) и юзер хочет в запросе извлекать строки без дополнительных запросов 'будет join таблиц' force_join_props
		 */
		if($this->isitlines && $this->force_join_props) {
			//НЕ делать каждый раз при $header->lines или $header->uclass запрос в базу
			$this->getDbCriteria()->with['lines'] = [];
			//никогда не джойнить таблицу строк во избежании задвоений при поиске и сортировке по свойствам, будет делать дополнительный запрос в таблицу строк
			$this->getDbCriteria()->with['lines'] = ['together'=>false];
			$this->getDbCriteria()->with['lines.property'] = ['together'=>false];
			$this->getDbCriteria()->with['uclass.properties'] = ['together'=>false];
		}
	}

	/**
	 * При добавлении новых свойств uProperties, они временно хранятся в этом массиве.
	 * При получении свойств uProperties, они временно хранятся в этом массиве.
	 * @var array
	 */
	private $_tmpUProperties = array();

	/**
	 * @var array названия свойств существующие у данного объекта
	 */
	private $_tmpUPropertiesNames = array();

	/**
	 * Получить свойства
	 * @param bool $force возвращает без кеширование на уровне объекта
	 * @return array
	 */
	public function getUProperties($force=false) {
		if(!$this->isitlines) return array();

		if($force) {
			$this->_tmpUProperties = array();
			$this->refresh();
		}

		if(!count($this->_tmpUProperties)) {
			$arrconfcms = Yii::app()->appcms->config;
			$uclassProperties = $this->uclass->properties;

			foreach($uclassProperties as $property) {
				if(isset($this->_tmpUProperties[$property->codename])===false) {
					$this->_tmpUProperties[$property->codename] = null;
				}
			}

			foreach($this->lines as $objline) {
				$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
				$this->_tmpUProperties[$objline->property->codename] = $objline->$namecolumn;
			}
		}
		return $this->_tmpUProperties;
	}

	/*
	 * Сохранить свойства в базе
	 *
	 */
	public function saveProperties() {
		$classproperties = $this->uclass->properties;
		$nameClassModelLines = $this->getActiveRelation('lines')->className;
		$arraylinesvalue = array();
		$arrconfcms = Yii::app()->appcms->config;
		foreach($this->lines as $objline) {
			$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
			$arraylinesvalue[$objline->property->codename] = array('objline' =>$objline, 'value' => $objline->$namecolumn, 'namecol' => $namecolumn);
		}
		foreach($classproperties as $objprop) {
			if(array_key_exists($objprop->codename, $this->_tmpUProperties)!==false) {
				if(array_key_exists($objprop->codename,$arraylinesvalue)!==false) {
					$arraylinesvalue[$objprop->codename]['objline']->$arraylinesvalue[$objprop->codename]['namecol'] = $this->_tmpUProperties[$objprop->codename];
					$arraylinesvalue[$objprop->codename]['objline']->save();
				}
				else {
					$newobjlines = $nameClassModelLines::create();
					$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objprop->myfield];
					$newobjlines->$namecolumn = $this->_tmpUProperties[$objprop->codename];
					$newobjlines->property_id = $objprop->primaryKey;
					$newobjlines->header_id = $this->primaryKey;
					$newobjlines->save();
				}
			}
		}
	}

	/**
	 * Редактирование ссылок - асоциативность объектов
	 * @param $type add, remove, clear
	 * @param $class
	 * @param array $idsheaders
	 * @param string $name_type_link - название типа ссылки (ссылка может хранится в другой таблице)
	 * @throws CException
	 */
	public function editlinks($type, $class, $idsHeader=null, $name_type_link=self::NAME_TYPE_LINK_BASE, $is_back=false) {
		$type_FROM = ($is_back==false)?'from':'to';
		$type_TO = ($is_back==false)?'to':'from';

		$addparam = [$type_FROM.'_class_id' => $this->uclass_id];

		$associationClass = (is_object($class))?$class:\uClasses::getclass($class);

		if(!$this->uclass->hasAssotiation($associationClass->codename,$is_back)) {
			throw new CException(Yii::t('cms','class '.$this->uclass->codename.' not association class '.$associationClass->codename));
		}

		$relatClass = ($is_back==false)?$this->uclass:$associationClass;

		$arrayModelLinks = $relatClass->getNamesModelLinks();
		if(!isset($arrayModelLinks[$name_type_link])) {
			throw new CException(Yii::t('cms','not space links key '.$name_type_link));
		}

		$nameRelate = (($is_back==false)?static::PRE_LINKS_MTM:static::PRE_LINKS_MTM_BACK).$name_type_link;

		if(!$this->metaData->hasRelation($nameRelate)) {
			$objLinkModel = $arrayModelLinks[$name_type_link]::model();
			$this->metaData->addRelation($nameRelate, array(self::MANY_MANY, $associationClass->getNameModelHeaderClass(), $objLinkModel->tableName() . '('.$type_FROM.'_obj_id, '.$type_TO.'_obj_id)'));
		}

		if($idsHeader) {
			$addparam[$type_TO.'_class_id'] = $associationClass->primaryKey;
		}

		$where=['and'];
		if($type!='and') {
			foreach($addparam as $k => $v) $where[] = $k.'='.$v;
		}

		$this->UserRelated->links_edit($type, $nameRelate, $idsHeader, $addparam, $where);
	}

	/**
	 * Получить ссылки на другие объекты
	 * @param mixed $class класс объекта
	 * @param string $name_type_link - название типа ссылки (ссылка может хранится в другой таблице)
	 * @return CActiveRecord[] - массив заголовков
	 * @throws CException
	 */
	public function getobjlinks($nameAssociationClass, $name_type_link=self::NAME_TYPE_LINK_BASE, $is_back=false) {
		$type_FROM = ($is_back==false)?'from':'to';
		$type_TO = ($is_back==false)?'to':'from';

		/* @var $associationClass uClasses */
		$associationClass = \uClasses::getclass($nameAssociationClass);

		if(!$this->uclass->hasAssotiation($associationClass->codename,$is_back)) {
			throw new CException(Yii::t('cms','class '.$this->uclass->codename.' not association class '.$associationClass->codename));
		}

		$objectModelAssociationClass = $associationClass->objects();

		$relatClass = ($is_back==false)?$this->uclass:$associationClass;

		$allRelateLinks = $relatClass->getNamesModelLinks();
		if(!isset($allRelateLinks[$name_type_link])) {
			throw new CException(Yii::t('cms','not space links key '.$name_type_link));
		}
		$nameModelLink = $allRelateLinks[$name_type_link];

		$nameRelate = (($is_back==false)?static::PRE_LINKS:static::PRE_LINKS_BACK).$name_type_link;

		if(!$this->metaData->hasRelation($nameRelate)) {
			$objectModelAssociationClass->metaData->addRelation($nameRelate, array(CActiveRecord::HAS_ONE, $nameModelLink, $type_TO . '_obj_id',
				'on' => $nameRelate . '.' . $type_TO . '_class_id=' . $associationClass->primaryKey . ' AND ' .
					$nameRelate . '.' . $type_FROM . '_obj_id=' . $this->primaryKey . ' AND ' .
					$nameRelate . '.' . $type_FROM . '_class_id=' . $this->uclass_id,
				'select' => false,
				'together' => true,
				'joinType' => 'INNER JOIN'
			));
		}

		return $objectModelAssociationClass->with($nameRelate);
	}

	protected function afterSave() {
		parent::afterSave();

		//если были изменены свойства то сохраняем их
		if(count($this->_tmpUProperties)) {
			$this->saveProperties();
		}
	}
	//именно перед удалением beforeDelete объекта нужно удалить его строки + доч.табл, ссылки + доч.табл
	public function beforeDelete() {
		if(!parent::beforeDelete()) return false;

		//del links
		foreach($this->uclass->association as $objClass) {
			foreach(array_keys($this->uclass->getNamesModelLinks()) as $typeLink) {
				$this->editlinks('clear', $objClass, null, $typeLink);
			}
		}
		//если есть обратные ссылки удалим и их
		foreach($this->uclass->association_back as $objClass) {
			foreach(array_keys($objClass->getNamesModelLinks()) as $typeLink) {
				$this->editlinks('clear', $objClass, null, $typeLink, true);
			}
		}

		return true;
	}

	public function beforeSave() {
		if(!parent::beforeSave()) return false;

		//для новых объектов необходимо подставить класс
		if($this->isNewRecord) $this->uclass_id = $this->uclass->primaryKey;
		return true;
	}

	public function hasProperty($name) {
		return in_array($name, $this->_tmpUPropertiesNames);
	}

	public function getUPropertyNames() {
		return $this->_tmpUPropertiesNames;
	}

	public function setUProperties($arrayName_Value) {
		if(!$this->hasProperty($arrayName_Value[0])) {
			throw new CException(
				Yii::t('cms','Not find prop {prop} this class {class}',
				array('{prop}'=>$arrayName_Value[0],'{class}'=>$this->uclass->codename))
			);
		}
		$this->_tmpUProperties[$arrayName_Value[0]] = $arrayName_Value[1];
	}

	public function beforeValidate() {
		if(!parent::beforeValidate()) return false;

		//uProperties
		if($this->isitlines) {
			$this->_formPropValid->attributes = $this->getUProperties();
			if ($this->_formPropValid->validate() == false) {
				$this->addErrors($this->_formPropValid->getErrors());
				return false;
			}
		}

		return true;
	}

	/**
	 * Установка критерии по для превдо-свойствам
	 * примеры:
	 * *Поиск:
	 * setCDbCriteriaUProp('condition','prop2','prop2=:val', 'AND') - аналог addCondition()
	 * -возможно указать простой запрос только с ОДНИМ свойством так как не стоит ЗЛОУПОРТЕРБЛЯТЬ поиском по свойствам
	 * *Сортировка
	 * setCDbCriteriaUProp('order','prop2','ASC')
	 * *Лимиты
	 * setCDbCriteriaUProp('limit','prop2',0,10)
	 * -Если метод setCDbCriteriaUProp уже используется то для установки лимитов использовать только этот метод
	 * -не нужно использовать ДВОЙНЫЕ сортировки поэтому этот метод перетерает order критерии всегда
	 *
	 *
	 * *НЕОБХОДИМО ВСЕГДА сохранять критерию если необходимо собирать запрос по цепочке
	 * -$saveCriteria = $modelObjects->getDbCriteria();
	 *
	 * @param $type
	 * @param $nameUProp
	 * @param $option1
	 * @param null $option2
	 * @return bool
	 * @throws CException
	 */
	public function getPropCriteria($type, $nameUProps, $option1, $option2=null) {
		if(!$this->isitlines) {
			throw new CException(Yii::t('cms','class '.$this->uclass->codename.' not support Uproperties'));
		}

		$config = Yii::app()->appcms->config;
		$relations = $this->relations();
		$criteria = new CDbCriteria;

		$thisClassProperties = [];
		foreach($this->uclass->properties as $prop) {
			$thisClassProperties[$prop->codename] = $prop;
		}

		if($type=='condition') {
			$this->force_join_props = true;

			$condition = $option1;
			$operator = $option2 ?: 'AND';
			$nameUProps = is_array($nameUProps)?$nameUProps:[$nameUProps];

			foreach($nameUProps as $nameUProp) {
				$objProp = $thisClassProperties[$nameUProp];
				$name_column = $config['TYPES_COLUMNS'][$objProp->myfield];
				$nameRelate = 'lines_find_'.$nameUProp;
				//HAS_ONE - у одного объекта может быть только одно строка определенного свойства!
				$this->metaData->addRelation('lines_find_'.$nameUProp, array(self::HAS_ONE, 'lines'.get_class($this), 'header_id', 'on'=> $nameRelate.'.property_id='.$objProp->primaryKey));
				$criteria->with['lines_find_'.$nameUProp] = array();

				$criteria->with['lines_find_'.$nameUProp]['select'] = false;
				$criteria->with['lines_find_'.$nameUProp]['together'] = true;
				$condition = str_replace($nameUProp, $nameRelate.'.'.$name_column, $condition);
			}

			$criteria->addCondition($condition, $operator);

			return $criteria;
		}

		$nameUProp = $nameUProps;
		$objProp = $thisClassProperties[$nameUProp];
		$name_column = $config['TYPES_COLUMNS'][$objProp->myfield];

		if($type=='order') {
			$this->force_join_props = true;

			$type_order = $option1;
			//HAS_ONE - у одного объекта может быть только одно строка определенного свойства!
			$this->metaData->addRelation('lines_sort', array(self::HAS_ONE, 'lines'.get_class($this), 'header_id', 'on'=> 'lines_sort.property_id='.$objProp->primaryKey));
			$criteria->with['lines_sort'] = array();

			$criteria->with['lines_sort']['select'] = false;
			$criteria->with['lines_sort']['together'] = true;
			//столбцы которые принадлежат не вошли в join будут содержать NULL поэтому для сортировки необходимо примести к другому типу
			$sql_query = '(CASE WHEN lines_sort.'.$name_column.' IS NULL THEN 1 ELSE 0 END) ASC, lines_sort.'.$name_column.' '.$type_order;
			//сама сортировка
			$criteria->order = $sql_query;

			return $criteria;
		}

	}

	protected function foreign_on_delete_cascade_MTM() {
		return array(
			'lines',
		);
	}

	/**
	 * @var MYOBJ\appscms\core\base\form\DForm null
	 */
	private $_formPropValid=null;

	public function afterCreate() {
		parent::afterCreate();

		//start uProperties
		if ($this->isitlines) {

			$objFormProp = MYOBJ\appscms\core\base\form\DForm::create();

			foreach($this->uclass->properties as $prop) {
				$nameProp = $prop->codename;

				$objFormProp->addAttributeRule($prop->codename, array('safe'), $this->uProperties[$nameProp]);

				$this->_tmpUPropertiesNames[] = $prop->codename;

				$arrconfcms = Yii::app()->appcms->config;

				if ($prop->minfield) $objFormProp->addAttributeRule($nameProp, array('length', 'min' => $prop->minfield));
				if ($prop->maxfield) $objFormProp->addAttributeRule($nameProp, array('length', 'max' => $prop->maxfield));
				if ($prop->required) $objFormProp->addAttributeRule($nameProp, array('required'));
				if ($prop->udefault) $objFormProp->addAttributeRule($nameProp, array('default', 'value' => $prop->udefault));

				$nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];
				if (array_key_exists($nametypef, $arrconfcms['rulesvalidatedef'])) {
					$addarrsett = array();
					$parsecvs = str_getcsv($prop->setcsv, "\n");
					foreach ($parsecvs as $keyval) {
						if (trim($keyval) == '') continue;
						if (strpos($keyval, 'us_set') === false) {
							if (strpos($keyval, '=>') === false) {
								array_push($addarrsett, $keyval);
							} else {
								list($typeval, $val) = explode('=>', trim($keyval));
								$addarrsett[$typeval] = $val;
							}
						}
					}
					if ($addarrsett) $objFormProp->addAttributeRule($nameProp, $addarrsett);
				}
				if ($nametypef == 'bool') $objFormProp->addAttributeRule($nameProp, array('boolean'));
				if ($nametypef == 'url') $objFormProp->addAttributeRule($nameProp, array('url'));
				if ($nametypef == 'email') $objFormProp->addAttributeRule($nameProp, array('email'));
			}

			$this->_formPropValid = $objFormProp;
		}
		//end uProperties
	}
}
