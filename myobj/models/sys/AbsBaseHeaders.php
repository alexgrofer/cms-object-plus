<?php
abstract class AbsBaseHeaders extends AbsBaseModel
{
	const PRE_PROP='prop_';

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
	 * @return string Название модели в которой лежит ссылки , пример linksObjectsAllMy
	 */
	public function getNameLinksModel() {
		return Yii::app()->appcms->config['spacescl'][$this->uclass->tablespace]['namelinksmodel'];
	}

	/**
	 * @var bool
	 * Данные Свойств лежат в объектах модели AbsBaseLines, если мы не собираемся использовать свойства то нужно поставить этот параметр false
	 */
	public $isitlines = true;
	/**
	 * @var bool Возможность создавать композицию объектов (ссылок объектов друг на друга)
	 * Если true тогда при каждом добавлении нового элемента для него будет создаваться новый объект класса AbsBaseLinksObjects таблица setcms_linksobjectsallmy
	 */
	public $flagAutoAddedLinks = true;

	public function relations() {
		$relations = [];

		$relations['uclass'] = array(self::BELONGS_TO, 'uClasses', 'uclass_id');

		if($this->isitlines == true) {
			$relations['lines'] = array(self::HAS_MANY, 'lines'.get_class($this), 'header_id');
		}
		return $relations;
	}

	public function beforeFind() {
		/*
		 * в случае если есть поддержка свойств(isitlines) и юзер хочет в запросе извлекать строки без дополнительных запросов 'будет join таблиц' force_join_props
		 */
		if($this->isitlines && $this->force_join_props) {
			$this->dbCriteria->with['lines.property'] = array();
			$this->dbCriteria->with['uclass.properties'] = array();
		}

		parent::beforeFind();
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
	 * @throws CException
	 */
	public function editlinks($type, $class, array $idsheaders=null) {
		$objects = null;

		if($idsheaders) {
			if(is_object($idsheaders)) $idsheaders = $idsheaders->primaryKey;
			if(!is_object($class)) {
				$class = uClasses::getclass($class);
			}
			$classid = $class->primaryKey;

			if(!$this->uclass->hasAssotiation($class->codename)) {
				throw new CException(Yii::t('cms','Not find assotiation class '.$class->codename));
			}

			$namelinkallmodel = $this->getNameLinksModel();
			$CRITERIA = new CDbCriteria();
			if(!is_array($idsheaders)) $idsheaders = array($idsheaders);
			$CRITERIA->addInCondition('idobj', $idsheaders);
			$CRITERIA->compare('uclass_id',$classid);
			$linksobjects = $namelinkallmodel::model()->findAll($CRITERIA);
			if(!$linksobjects) {
				throw new CException(Yii::t('cms','Not find link id {idlink}, Class {class}, table_links "{nametable}"',
				array('{class}'=>$class->codename, '{idlink}'=>implode(',',$idsheaders),'{nametable}'=>$this->getNameLinksModel())));
			}
			$objects = apicms\utils\arrvaluesmodel($linksobjects,'id');
		}

		$this->toplink->UserRelated->links_edit($type,'links',$objects);
	}

	/**
	 * Получить ссылки на другие объекты
	 * @param mixed $class класс объекта
	 * @param string $tableSpace табличное пространство
	 * @return AbsBaseHeaders возвращает модель с настроенной criteria
	 * @throws CException
	 */
	public function getobjlinks($class,string $tableSpace=null) {
		$objectcurrentlink = $this->toplink;
		if(!$objectcurrentlink) {
			throw new CException(Yii::t('cms','Not find link id {idlink}, Class "{class}", table_links "{nametable}"',
			array('{class}'=>$this->uclass_id, '{idlink}'=>$this->primaryKey,'{nametable}'=>$this->getNameLinksModel())));
		}
		$objclass = \uClasses::getclass($class);

		if(!$this->uclass->hasAssotiation($objclass->codename)) {
			throw new CException(Yii::t('cms','Not find assotiation class '.$class->codename));
		}

		//проверить вернул ли класс, а то не поймет что за ошибка была даже если выскочит
		//сделать путь для сообщений cms-ки, будут ли работать yii
		//throw new CException(Yii::t('cms','Property "{class}.{property}" is not defined.',
			//array('{class}'=>get_class($this), '{property}'=>$name)));
		$idsheaders = apicms\utils\arrvaluesmodel($objectcurrentlink->links,'idobj');
		$nameModelHeader = Yii::app()->appcms->config['spacescl'][$objclass->tablespace]['namemodel'];
		$model = new $nameModelHeader();
		$model->dbCriteria->addInCondition($model->tableAlias.'.id', $idsheaders);
		$model->dbCriteria->compare($model->tableAlias.'.uclass_id',$objclass->primaryKey);
		$model->uclass_id = $objclass->primaryKey;
		return $model;
	}

	public function afterSave() {
		if(parent::afterSave()!==false) {
			//Если flagAutoAddedLinks=true, после создания объекта создаем линк в (таблице ссылок для объектов).
			//С этим параметром можно работать как для нового так и для существующего объекта, т.е если раньше у объекта небыло возможность связки
			//то эту возможность можно реализовать в любой момент
			if($this->flagAutoAddedLinks) {
				if(!$this->toplink) {
					$namelinkallmodel = $this->getNameLinksModel();
					$objectcurrentlink = new $namelinkallmodel();
					$objectcurrentlink->idobj = $this->primaryKey;
					$objectcurrentlink->uclass_id = $this->uclass_id;
					$objectcurrentlink->save();

					//toplink это реляционная таблица, будет инициализированна
					$this->toplink = $this->getRelated('toplink', true);
				}
			}

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
		$objectCurrentLink = $this->toplink;
		if($objectCurrentLink) {
			if(count($objectCurrentLink->links)) {
				//ссылки объектов
				$objectCurrentLink->UserRelated->links_edit('clear','links');
			}
			//удалить ведущую ссылку, благодаря ей возможна привязка объектов разных классов и разных табличных пространств
			$objectCurrentLink->delete();
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
	public function setAttributes($values) {
		parent::setAttributes($values);

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

		//+++реляция для ссылки возможна только после того как инициализован класс
		$nameLinksModel = $this->getNameLinksModel();
		$this->metaData->addRelation('toplink', array(self::HAS_ONE, $nameLinksModel, 'idobj', 'on'=> 'uclass_id='.$this->uclass->primaryKey));

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
		$this->oldProperties = $this->uProperties;
		//пройтись по критерии и если там встречается параметр "_uProp" тогда добавить некоторые дополнительные критерии для нормальног опоиска
	}

	/**
	 * Названия реляций.
	 * При каждом новом условии добавляется в массив, псевдо-свойства необходимо джойнить еще раз таблицу строк.
	 * @var array
	 */
	protected $_finderUProp=array();
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
	public function setSetupCriteria($configArray) {
		$type = $configArray[0];
		$nameUProp = $configArray[1];
		$option1 = $configArray[2];
		$option2 = isset($configArray[3])?$configArray[3]:null;

		$config = Yii::app()->appcms->config;
		$relations = $this->relations();

		if($type=='limit') {
			parent::setSetupCriteria($configArray);

			return true;
		}

		$thisClassProperties = [];
		foreach($this->uclass->properties as $prop) {
			$thisClassProperties[$prop->codename] = $prop;
		}

		if(!isset($thisClassProperties[$nameUProp])) {
			throw new CException(Yii::t('cms','None prop "{prop}" object class  "{class}"',
				array('{prop}'=>$nameUProp, '{class}'=>$this->uclass->codename)));
		}

		$objProp = $thisClassProperties[$nameUProp];
		$name_column = $config['TYPES_COLUMNS'][$objProp->myfield];

		if($type=='condition') {
			$value = $option1;
			$operator = $option2 ?: 'AND';

			$keyLinesProp = array_search($nameUProp, $this->_finderUProp);
			if(in_array($nameUProp, $this->_finderUProp)===false) {
				$this->_finderUProp[] = $nameUProp;
				$keyLinesProp = count($this->_finderUProp)-1;
				$this->metaData->addRelation('lines_find_'.$keyLinesProp, $relations['lines']);
			}
			$nameRelate = 'lines_find_'.$keyLinesProp;

			$this->getDbCriteria()->with['lines_find_'.$keyLinesProp]['together'] = true;
			$this->getDbCriteria()->with['lines_find_'.$keyLinesProp]['select'] = false;
			$this->getDbCriteria()->with['lines_find_'.$keyLinesProp]['condition'] = $nameRelate.'.property_id='.$objProp->primaryKey.' OR '.$nameRelate.'.id IS NULL';

			//для того что бы не попали лишнии строки(проблемы limit) при джойне ограничим только нужным свойством которое учавствует в поиске
			$condition = $nameRelate.'.'.$name_column.str_replace($nameUProp, '', $value).' AND '.$nameRelate.'.property_id='.$objProp->primaryKey;
			$this->getDbCriteria()->addCondition($condition, $operator);

			return true;
		}

		if($type=='order') {
			$type_order = $option1;

			$this->metaData->addRelation('lines_sort', $relations['lines']);
			$this->getDbCriteria()->with['lines_sort']['select'] = false;

			$this->getDbCriteria()->with['lines_sort']['together'] = true;
			//столбцы которые принадлежат не вошли в join будут содержать NULL поэтому для сортировки необходимо примести к другому типу
			$sql_query = '(CASE WHEN lines_sort.'.$name_column.' IS NULL THEN 1 ELSE 0 END) ASC, lines_sort.'.$name_column.' '.$type_order;
			//сама сортировка
			$this->getDbCriteria()->with['lines_sort']['order'] = $sql_query;
			//для того что бы не попали лишнии строки(проблемы limit) при джойне ограничим только нужным свойством которое учавствует в сортировке
			$this->getDbCriteria()->with['lines_sort']['condition'] = 'lines_sort.property_id='.$objProp->primaryKey.' OR lines_sort.id IS NULL';

			return true;
		}

	}
}
