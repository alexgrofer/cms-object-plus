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
	public function relations()
	{
		$namemodellines = str_replace('Headers','',get_class($this));

		$arr_relationsdef = array('uclass'=>array(self::BELONGS_TO, 'uClasses', 'uclass_id')); // uclass = models.ForeignKey(uClasses))
		if($this->isitlines == true) {
			$arr_relationsdef['lines'] = array(self::HAS_MANY, $namemodellines.'Lines', 'header_id');
			//для поиска по свойствам
			$arr_relationsdef['lines_sort'] = $arr_relationsdef['lines'];
			//для сортировки по свойствам
			$arr_relationsdef['lines_find'] = $arr_relationsdef['lines'];
		}
		return $arr_relationsdef;
	}

	public function beforeFind() {
		if($this->isitlines) {
			if($this->force_join_props) {
				$this->dbCriteria->with['lines.property'] = array();
				$this->dbCriteria->with['uclass.properties'] = array();
			}
			else {
				unset($this->dbCriteria->with['lines.property']);
				unset($this->dbCriteria->with['uclass.properties']);
			}
		}
		parent::beforeFind();
	}

	/**
	 * При добавлении новых свойств set_properties, они временно хранятся в этом массиве.
	 * При получении свойств get_properties, они временно хранятся в этом массиве.
	 * @var array
	 */
	private $_tmpProperties = array();

	/**
	 * @var array названия свойств существующие у данного объекта
	 */
	private $_tmpPropertiesNames = array();

	/**
	 * Получить свойства
	 * @param bool $force возвращает без кеширование на уровне объекта
	 * @return array
	 */
	public function get_properties($force=false) {
		if(!count($this->_tmpProperties) || $force==true) {
			$arrconfcms = Yii::app()->appcms->config;
			$classproperties = $this->uclass->properties;
			$arraylinesvalue = array();
			foreach($this->lines as $objline) {
				$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
				$arraylinesvalue[$objline->property->codename] = array('objline' =>$objline, 'value' => $objline->$namecolumn, 'namecol' => $namecolumn);
			}
			if(count($classproperties)) {

				foreach($classproperties as $objprop) {
					$this->_tmpProperties[$objprop->codename] = (array_key_exists($objprop->codename,$arraylinesvalue)!==false)?$arraylinesvalue[$objprop->codename]['value']:'';
				}
			}
			$this->_tmpPropertiesNames = array_keys($this->_tmpProperties);
		}
		return $this->_tmpProperties;
	}

	/*
	 * Сохранить свойства в базе
	 *
	 */
	public function saveProperties() {
		$classproperties = $this->uclass->properties;
		$namemodellines = str_replace('Headers','',get_class($this)).'Lines';
		$arraylinesvalue = array();
		$arrconfcms = Yii::app()->appcms->config;
		foreach($this->lines as $objline) {
			$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
			$arraylinesvalue[$objline->property->codename] = array('objline' =>$objline, 'value' => $objline->$namecolumn, 'namecol' => $namecolumn);
		}
		foreach($classproperties as $objprop) {
			//если не изменял свойство не нужно каждый раз делать запрос
			if($this->old_properties[$objprop->codename]==$this->_tmpProperties[$objprop->codename]) continue;
			if(array_key_exists($objprop->codename, $this->_tmpProperties)!==false) {
				if(array_key_exists($objprop->codename,$arraylinesvalue)!==false) {
					$arraylinesvalue[$objprop->codename]['objline']->$arraylinesvalue[$objprop->codename]['namecol'] = $this->_tmpProperties[$objprop->codename];
					$arraylinesvalue[$objprop->codename]['objline']->save();
				}
				else {
					$newobjlines = new $namemodellines();
					$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objprop->myfield];
					$newobjlines->$namecolumn = $this->_tmpProperties[$objprop->codename];
					$newobjlines->property_id = $objprop->id;
					$newobjlines->header_id = $this->id;
					$newobjlines->save();
				}
			}
		}
		//передать список имен свойств при новом созранении, т.к возможно были добавленны новые свойства
		$this->_tmpPropertiesNames = array_keys($this->_tmpProperties);
		//теперь старые данные полностью переписанны
		$this->old_properties = $this->get_properties();
	}
	private $_tempthislink;

	/*
	 * Есть таблица ссылок setcms_linksobjectsallmy.
	 * При создании объекта всегда создается ссылка. это поведение управляется параметром flagAutoAddedLinks, читать там
	 * она позволяет цеплять обекты одного класса к другому.
	 * Таблица ссылается сама на себя через таблицу связку setcms_linksobjectsallmy_links.
	 */
	private function _getobjectlink() {
		if(empty($this->_tempthislink)) {
			$namelinkallmodel = $this->getNameLinksModel();
			$objectcurrentlink = $namelinkallmodel::model()->findByAttributes(array('idobj' => $this->id, 'uclass_id' => $this->uclass_id));
			$this->_tempthislink = $objectcurrentlink;
		}
		if(!$this->_tempthislink) return false;
		return $this->_tempthislink;
	}
	public function editlinks($type, $class, $idsheaders) {
		if(is_object($idsheaders)) $idsheaders = $idsheaders->id;
		if(!is_object($class)) {
			$class = uClasses::getclass($class);
		}
		$classid = $class->id;
		$namelinkallmodel = $this->getNameLinksModel();
		$objectcurrentlink = $this->_getobjectlink();
		$CRITERIA = new CDbCriteria();
		if(!is_array($idsheaders)) $idsheaders = array($idsheaders);
		$CRITERIA->addInCondition('idobj', $idsheaders);
		$CRITERIA->compare('uclass_id',$classid);
		$linksobjects = $namelinkallmodel::model()->findAll($CRITERIA);
		if(!$linksobjects) {
			throw new CException(Yii::t('cms','Not find link id {idlink}, Class "{class}, table_links "{nametable}"',
			array('{class}'=>$class->name, '{idlink}'=>implode(',',$idsheaders),'{nametable}'=>$this->getNameLinksModel())));
		}

		$objectcurrentlink->UserRelated->links_edit($type,'links',apicms\utils\arrvaluesmodel($linksobjects,'id'));
	}
	public function getobjlinks($class) {
		$objectcurrentlink = $this->_getobjectlink();
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

	//после создания объекта создаем линк в (таблице ссылок для объектов) для работы со ссылками можду классами
	public function afterSave() {
		if(parent::afterSave()!==false) {
			if($this->flagAutoAddedLinks) {
				$namelinkallmodel = $this->getNameLinksModel();
				$objectcurrentlink = $this->_getobjectlink();
				if(!$objectcurrentlink) {
					$objectcurrentlink = new $namelinkallmodel();
					$objectcurrentlink->idobj = $this->id;
					$objectcurrentlink->uclass_id = $this->uclass_id;
					$objectcurrentlink->save();
				}
			}

			//если были изменены свойства то сохраняем их
			if(count($this->_tmpProperties)) {
				$this->saveProperties();
			}
			return true;
		}
		else return parent::afterSave();
	}
	//именно перед удалением beforeDelete объекта нужно удалить его строки + доч.табл, ссылки + доч.табл
	function beforeDelete() {
		//запрет на удаление отдельных объектов системы
		if(isset(Yii::app()->appcms->config['controlui']['none_del']['objects'][$this->uclass->codename])) {
			foreach(Yii::app()->appcms->config['controlui']['none_del']['objects'][$this->uclass->codename] as $key => $val) {
				if($this->$key==$val) return false;
			}
		}
		//del lines
		if($this->isitlines == true && count($this->lines)) {
			$this->clearMTMLink('lines', Yii::app()->appcms->config['sys_db_type_InnoDB']); //очистить ссылки на строки в дочерней тиблице
			//удалить строки этого объекта
			//в таблице строк объекта мы ничего не знаем о объекте так как работаем через настраиваемую таблицу строк, ключа объекта в ней нет, поэтому возможет только подобный метод удаления
			$idslines = apicms\utils\arrvaluesmodel($this->lines, 'id');
			$CRITERIA = new CDbCriteria();
			$CRITERIA->addInCondition('id', $idslines);
			$this->lines[0]->model()->deleteAll($CRITERIA);
		}
		//del links
		$objectcurrentlink = $this->_getobjectlink();
		if($objectcurrentlink) {
			if(count($objectcurrentlink->links)) {
				$objectcurrentlink->clearMTMLink('links', Yii::app()->appcms->config['sys_db_type_InnoDB']); //очистить ссылки на *ссылки в дочерней тиблице
			}
			$objectcurrentlink->delete(); //удалить ссылку
		}
		return parent::beforeDelete();
	}

	public function beforeSave() {
		if(parent::beforeSave()!==false) {
			if($this->isNewRecord && method_exists(get_class($this),'get_properties')) $this->uclass_id = $this->uclass->id;
			return true;
		}
		else return parent::beforeSave();
	}

	public function hasProperty($name) {
		return in_array($name, $this->_tmpPropertiesNames);
	}

	public function propertyNames() {
		return $this->_tmpPropertiesNames;
	}
	private $_tmp_ClassProperties = array();
	public function getClassProperties() {
		if(!$this->_tmp_ClassProperties) {
			foreach($this->uclass->properties as $prop) {
				$this->_tmp_ClassProperties[$prop->codename] = $prop;
			}
		}
		return $this->_tmp_ClassProperties;
	}
	public function set_properties($name,$value) {
		if(!$this->hasProperty($name)) {
			throw new CException(
				Yii::t('cms','Not find prop {prop}',
				array('{prop}'=>$name))
			);
		}
		$this->_tmpProperties[$name] = $value;
		$this->{$name.'prop_'} = $value;
	}

	public function setAttributes($values) {
		parent::setAttributes($values);

		if(is_array($values) && count($values)) {
			//SWITCH MY TYPES
			foreach($values as $nameElem => $val) {
				//CASE type Prop
				if(($pos = strpos($nameElem,'prop_'))!==false) {
					$this->set_properties(substr($nameElem,0,$pos),$val);
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
			$currentproperties = $this->get_properties();
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
	protected $old_properties=array();
	public function declareObj() {
		parent::declareObj();
		//добавляем свойтсва к модели
		if($this->isitlines) {
			$arrconfcms = Yii::app()->appcms->config;
			$currentproperties = $this->get_properties();
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
	public function initObj() {
		parent::initObj();
		$this->old_properties = $this->get_properties();
	}
}
