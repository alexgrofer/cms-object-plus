<?php
abstract class AbsBaseHeaders extends AbsBaseModel
{
	const PRE_PROP='prop_';
	const PRE_LINKS='links_';
	const PRE_LINKS_MTM='links_mtm_';

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
		return 'setcms_'.strtolower(get_class($this));
	}

	/**
	 * @var bool В запросе будет join нить таблицы при работе со свойствами.
	 * если false и свойства используются тогда будет запрос при каждом вызов списка свойств
	 * false - лучше использовать когда мы точно не собираемся вызывать свойства на данном этапе выборки
	 */
	public $force_join_props = true;

	/**
	 * Список моделий для связки объектов
	 * @return array
	 */
	public function getNamesModelLinks() {
		return Yii::app()->appcms->config['spacescl'][$this->uclass->tablespace]['nameModelLinks'];
	}

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

	protected function defaultRules() {
		$rules = parent::defaultRules();
		return array_merge($rules, array(
			array('uclass_id', 'unsafe'),
		));
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
			//если не изменял свойство не нужно каждый раз делать запрос,??? тут могут быть большие текстровые строки может быть проблема со скоростью
			//лучше использовать какие то возможности клиента
			if(!$this->isNewRecord && ($this->oldProperties[$objprop->codename]==$this->_tmpUProperties[$objprop->codename])) continue;
			if(array_key_exists($objprop->codename, $this->_tmpUProperties)!==false) {
				if(array_key_exists($objprop->codename,$arraylinesvalue)!==false) {
					$arraylinesvalue[$objprop->codename]['objline']->$arraylinesvalue[$objprop->codename]['namecol'] = $this->_tmpUProperties[$objprop->codename];
					$arraylinesvalue[$objprop->codename]['objline']->save();
				}
				else {
					$newobjlines = new $nameClassModelLines();
					$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objprop->myfield];
					$newobjlines->$namecolumn = $this->_tmpUProperties[$objprop->codename];
					$newobjlines->property_id = $objprop->primaryKey;
					$newobjlines->header_id = $this->primaryKey;
					$newobjlines->save();
				}
			}
		}

		//теперь старые данные полностью переписанны
		$this->oldProperties = $this->uProperties;
	}

	/**
	 * Редактирование ссылок - асоциативность объектов
	 * @param $type add, remove, clear
	 * @param $class
	 * @param array $idsheaders
	 * @param string $name_type_link - название типа ссылки (ссылка может хранится в другой таблице)
	 * @throws CException
	 */
	public function editlinks($type, $class, $idsHeader=null, $name_type_link='0') {
		$addparam = ['from_class_id' => $this->uclass_id];

		$associationClass = (is_object($class))?$class:\uClasses::getclass($class);

		if(!$this->uclass->hasAssotiation($associationClass->codename)) {
			throw new CException(Yii::t('cms','class '.$this->uclass->codename.' not association class '.$associationClass->codename));
		}

		$nameRelate = static::PRE_LINKS_MTM.$name_type_link;

		if(!$this->metaData->hasRelation($nameRelate)) {
			$arrayModelLinks = $this->getNamesModelLinks();
			$objLinkModel = $arrayModelLinks[$name_type_link]::model();
			$this->metaData->addRelation($nameRelate, array(self::MANY_MANY, $associationClass->getNameModelHeaderClass(), $objLinkModel->tableName() . '(from_obj_id, to_obj_id)'));
		}

		if($idsHeader) {
			$addparam['to_class_id'] = $associationClass->primaryKey;
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
	public function getobjlinks($nameAssociationClass, $name_type_link='0') {

		/* @var $associationClass uClasses */
		$associationClass = \uClasses::getclass($nameAssociationClass);

		if(!$this->uclass->hasAssotiation($associationClass->codename)) {
			throw new CException(Yii::t('cms','class '.$this->uclass->codename.' not association class '.$associationClass->codename));
		}

		$objectModelAssociationClass = $associationClass->objects();

		$allRelateLinks = $this->getNamesModelLinks();
		if(!isset($allRelateLinks[$name_type_link])) {
			throw new CException(Yii::t('cms','not space links key '.$name_type_link));
		}
		$nameModelLink = $allRelateLinks[$name_type_link];
		$nameRelate = static::PRE_LINKS.$name_type_link;
		$objectModelAssociationClass->metaData->addRelation($nameRelate, array(CActiveRecord::HAS_ONE, $nameModelLink, 'to_obj_id',
			'on'=> $nameRelate.'.to_class_id='.$associationClass->primaryKey.' AND '.
				$nameRelate.'.from_obj_id='.$this->primaryKey.' AND '.
				$nameRelate.'.from_class_id='.$this->uclass_id,
			'select' => false,
			'together' => true,
			'joinType'=>'INNER JOIN'
		));

		return $objectModelAssociationClass->with($nameRelate);
	}

	public function afterSave() {
		if(parent::afterSave()!==false) {
			//если были изменены свойства то сохраняем их
			if(count($this->_tmpUProperties)) {
				$this->saveProperties();
			}
			return true;
		}
		else return parent::afterSave();
	}
	//именно перед удалением beforeDelete объекта нужно удалить его строки + доч.табл, ссылки + доч.табл
	public function beforeDelete() {
		//запрет на удаление отдельных объектов системы
		if(isset(Yii::app()->appcms->config['controlui']['none_del']['objects'][$this->uclass->codename])) {
			foreach(Yii::app()->appcms->config['controlui']['none_del']['objects'][$this->uclass->codename] as $key => $val) {
				if(is_array($val)) {
					$delCount=0;
					foreach($val as $kkey => $vval) {
						if($this->$kkey==$vval) {
							$delCount++;
						}
					}
					if($delCount=count($vval)) {
						return false;
					}
				}
				elseif($this->$key==$val) {
					return false;
				}
			}
		}
		//del lines
		if($this->isitlines == true && count($this->lines)) {
			//удалим строки
			$this->clearMTMLink('lines', Yii::app()->appcms->config['sys_db_type_InnoDB']);
		}
		//del links
		foreach($this->uclass->association as $objClass) {
			$this->editlinks('clear', $objClass);
		}
		return parent::beforeDelete();
	}

	public function beforeSave() {
		if(parent::beforeSave()!==false) {
			//для новых объектов необходимо подставить класс
			if($this->isNewRecord) $this->uclass_id = $this->uclass->primaryKey;
			return true;
		}
		else return parent::beforeSave();
	}

	public function hasProperty($name) {
		return in_array($name, $this->_tmpUPropertiesNames);
	}

	public function propertyNames() {
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
		$this->{$arrayName_Value[0].self::PRE_PROP} = $arrayName_Value[1];
	}

	/**
	 * Добавление сеттера
	 *
	 * //CASE type Prop
	 * Из формы hrml $obj->attributes = $_POST свайства попадают в аттрибуты (пример name_string_1.self::PRE_PROP) тут мы их ловим и передаем в $this->uProperties для дальнейшей
	 * бработки и сохранения.
	 * В ручном режиме конечно можно работать и на прямую через uProperties или setUProperties
	 *
	 * @param $values
	 */
	public function setAttributes($values, $safeOnly=true) {
		parent::setAttributes($values, $safeOnly);

		if(is_array($values) && count($values)) {
			//SWITCH MY TYPES
			foreach($values as $nameElem => $val) {
				//CASE type Prop
				if(($pos = strpos($nameElem,self::PRE_PROP))!==false) {
					$this->uProperties = [substr($nameElem,0,$pos), $val];
				}
				//CASE type new type
				//if(...)
			}
		}
	}

	/**
	 * При изменении свойств до записи, иногда необходимо знать что было раньше до изменения
	 * @var array
	 */
	protected $oldProperties=array();
	public function getOldProperties() {
		return $this->oldProperties;
	}

	public function declareObj() {
		parent::declareObj();

		//+++необходимо узнать список свойств у этого объекта
		foreach($this->uclass->properties as $prop) {
			//для списка названий свойств этого объекта
			$this->_tmpUPropertiesNames[] = $prop->codename;
		}

		//+++добавляем свойтсва к модели
		if($this->isitlines) {
			$arrconfcms = Yii::app()->appcms->config;
			$currentproperties = $this->uProperties;
			foreach($this->uclass->properties as $prop) {
				$nameelem = $prop->codename.self::PRE_PROP;
				//инициализируем свойство
				$valProp = (isset($currentproperties[$prop->codename]))?$currentproperties[$prop->codename]:null;
				$this->addElemClass($nameelem,$valProp);
				//устанавливаем правила валидации
				if($prop->minfield) $this->customRules[] = array($nameelem, 'length', 'min'=>$prop->minfield);
				if($prop->maxfield) $this->customRules[] = array($nameelem, 'length', 'max'=>$prop->maxfield);
				if($prop->required) $this->customRules[] = array($nameelem, 'required');
				if($prop->udefault) $this->customRules[] = array($nameelem, 'default', 'value'=>$prop->udefault);

				$nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];
				/*
				 * для некоторый свойство возможна тонкая настройка type=>string
				 */
				if(array_key_exists($nametypef, $arrconfcms['rulesvalidatedef'])) {
					$addarrsett = array($nameelem);
					$parsecvs = str_getcsv($prop->setcsv,"\n");
					foreach($parsecvs as $keyval) {
						if(trim($keyval)=='') continue;
						if(strpos($keyval,'us_set')===false) {
							if(strpos($keyval,'=>')===false) {
								array_push($addarrsett,$keyval);
							}
							else {
								list($typeval,$val) = explode('=>',trim($keyval));
								$addarrsett[$typeval] = $val;
							}
						}
					}
					$this->customRules[] = (count($addarrsett)==1) ? array($nameelem, 'safe') : $addarrsett;
				}
				//для остальных нужно прописать safe иначе не будут отображаться в редактировании объекта
				else {
					$this->customRules[] = array($nameelem, 'safe');
				}
				if($nametypef=='bool') $this->customRules[] = array($nameelem, 'boolean');
				if($nametypef=='url') $this->customRules[] = array($nameelem, 'url');
				if($nametypef=='email') $this->customRules[] = array($nameelem, 'email');

				//добавить в типы полей формы элементы для свойств
				$nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];
				$this->customElementsForm[$nameelem] = array('type' => $arrconfcms['TYPES_MYFIELDS'][$nametypef]);
			}
		}
	}
	public function initObj() {
		parent::initObj();
		if($this->isitlines) {
			$this->oldProperties = $this->uProperties;
		}
		//пройтись по критерии и если там встречается параметр "_uProp" тогда добавить некоторые дополнительные критерии для нормальног опоиска
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
}
