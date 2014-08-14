<?php
abstract class AbsBaseHeaders extends AbsBaseModel
{
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
			//для поиска по свойствам
			$relations['lines_sort'] = $relations['lines'];
			//для сортировки по свойствам
			$relations['lines_find'] = $relations['lines'];
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
			$classproperties = $this->uclass->properties;
			$arraylinesvalue = array();
			foreach($this->lines as $objline) {
				$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
				$arraylinesvalue[$objline->property->codename] = array('objline' =>$objline, 'value' => $objline->$namecolumn, 'namecol' => $namecolumn);
			}
			if(count($classproperties)) {

				foreach($classproperties as $objprop) {
					$this->_tmpUProperties[$objprop->codename] = (array_key_exists($objprop->codename,$arraylinesvalue)!==false)?$arraylinesvalue[$objprop->codename]['value']:'';
				}
			}
			$this->_tmpUPropertiesNames = array_keys($this->_tmpUProperties);
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
					$newobjlines->property_id = $objprop->id;
					$newobjlines->header_id = $this->id;
					$newobjlines->save();
				}
			}
		}

		//теперь старые данные полностью переписанны
		$this->oldProperties = $this->uProperties;
	}

	public function editlinks($type, $class, $idsheaders=null) {
		$objects = null;
		$objectcurrentlink = $this->toplink;

		if($idsheaders) {
			if(is_object($idsheaders)) $idsheaders = $idsheaders->id;
			if(!is_object($class)) {
				$class = uClasses::getclass($class);
			}
			$classid = $class->id;
			$namelinkallmodel = $this->getNameLinksModel();
			$CRITERIA = new CDbCriteria();
			if(!is_array($idsheaders)) $idsheaders = array($idsheaders);
			$CRITERIA->addInCondition('idobj', $idsheaders);
			$CRITERIA->compare('uclass_id',$classid);
			$linksobjects = $namelinkallmodel::model()->findAll($CRITERIA);
			if(!$linksobjects) {
				throw new CException(Yii::t('cms','Not find link id {idlink}, Class "{class}, table_links "{nametable}"',
				array('{class}'=>$class->name, '{idlink}'=>implode(',',$idsheaders),'{nametable}'=>$this->getNameLinksModel())));
			}
			$objects = apicms\utils\arrvaluesmodel($linksobjects,'id');
		}

		$objectcurrentlink->UserRelated->links_edit($type,'links',$objects);
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
			array('{class}'=>$this->uclass_id, '{idlink}'=>$this->id,'{nametable}'=>$this->getNameLinksModel())));
		}
		$objclass = \uClasses::getclass($class);
		//проверить вернул ли класс, а то не поймет что за ошибка была даже если выскочит
		//сделать путь для сообщений cms-ки, будут ли работать yii
		//throw new CException(Yii::t('cms','Property "{class}.{property}" is not defined.',
			//array('{class}'=>get_class($this), '{property}'=>$name)));
		$idsheaders = apicms\utils\arrvaluesmodel($objectcurrentlink->links,'idobj');
		$nameModelHeader = Yii::app()->appcms->config['spacescl'][$objclass->tablespace]['namemodel'];
		$model = new $nameModelHeader();
		$model->dbCriteria->addInCondition($model->tableAlias.'.id', $idsheaders);
		$model->dbCriteria->compare($model->tableAlias.'.uclass_id',$objclass->id);
		$model->uclass_id = $objclass->id;
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
					$objectcurrentlink->idobj = $this->id;
					$objectcurrentlink->uclass_id = $this->uclass_id;
					$objectcurrentlink->save();
					/*
					 * toplink это реляционная таблица, что бы полностью не перезагружать ($this->refresh()) объект просто инициализируем свойство новым объектов
					 */
					$this->toplink = $objectcurrentlink;
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
				$objectCurrentLink->clearMTMLink('links', Yii::app()->appcms->config['sys_db_type_InnoDB']);
			}
			//удалить ведущую ссылку, благодаря ей возможна привязка объектов разных классов и разных табличных пространств
			$objectCurrentLink->delete();
		}
		return parent::beforeDelete();
	}

	public function beforeSave() {
		if(parent::beforeSave()!==false) {
			//для новых объектов необходимо подставить класс
			if($this->isNewRecord) $this->uclass_id = $this->uclass->id;
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
				Yii::t('cms','Not find prop {prop}',
				array('{prop}'=>$arrayName_Value[0]))
			);
		}
		$this->_tmpUProperties[$arrayName_Value[0]] = $arrayName_Value[1];
		$this->{$arrayName_Value[0].'prop_'} = $arrayName_Value[1];
	}

	public function setAttributes($values) {
		parent::setAttributes($values);

		if(is_array($values) && count($values)) {
			//SWITCH MY TYPES
			foreach($values as $nameElem => $val) {
				//CASE type Prop
				if(($pos = strpos($nameElem,'prop_'))!==false) {
					$this->uProperties = [substr($nameElem,0,$pos), $val];
				}
				//CASE type new type
				//if(...)
			}
		}
	}

	protected function dinamicModel() {
		parent::dinamicModel();
		//добавляем свойтсва к модели
		if($this->isitlines) {
			$arrconfcms = Yii::app()->appcms->config;
			$currentproperties = $this->uProperties;
			foreach($this->uclass->properties as $prop) {
				$nameelem = $prop->codename.'prop_';
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
					$this->customRules[] = $addarrsett;
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

	/**
	 * При изменении свойств до записи, иногда необходимо знать что было раньше до изменения
	 * @var array
	 */
	protected $oldProperties=array();
	public function getOldProperties() {
		return $this->oldProperties;
	}

	public function declareObj() {
		$nameLinksModel = $this->getNameLinksModel();
		$this->metaData->addRelation('toplink', array(self::HAS_ONE, $nameLinksModel, 'idobj', 'on'=> 'uclass_id='.$this->uclass_id));

		parent::declareObj();
		//добавляем свойтсва к модели
		if($this->isitlines) {
			$arrconfcms = Yii::app()->appcms->config;
			$currentproperties = $this->uProperties;
			foreach($this->uclass->properties as $prop) {
				$nameelem = $prop->codename.'prop_';
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
	}
}
