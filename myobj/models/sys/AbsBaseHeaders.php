<?php
abstract class AbsBaseHeaders extends AbsModel // (Django) class AbsBaseHeaders(models.Model):
{
	public $uclass_id;
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	protected function getNameLinksModel() {
		return Yii::app()->appcms->config['spacescl'][$this->uclass->tablespace]['namelinksmodel'];
	}
	protected $isitlines = true; // is true lines this object model
	public $flagAutoAddedLinks = true; // creade links from create object
	public function relations()
	{
		$namemodellines = str_replace('Headers','',get_class($this));
		$arr_relationsdef = array('uclass'=>array(self::BELONGS_TO, 'uClasses', 'uclass_id')); // uclass = models.ForeignKey(uClasses))
		if($this->isitlines == true) {
			$arr_relationsdef['lines'] = array(self::MANY_MANY, $namemodellines.'Lines',
				'setcms_'.strtolower($namemodellines).'headers_lines(from_headers_id, to_lines_id)'); // lines = models.ManyToManyField(myObjLines,blank=True)
			$arr_relationsdef['lines_alias'] = $arr_relationsdef['lines'];
			$arr_relationsdef['lines_order'] = $arr_relationsdef['lines'];$arr_relationsdef['lines_order2'] = $arr_relationsdef['lines'];
		}
		return $arr_relationsdef;
	}
	//user
	public $isHeaderModel=true;
	/**
	 * @var bool Не будет дополнительных запросов но будет join, стоит использовать в списках
	 */
	private $_is_force_prop = false;
	public function set_force_prop($flag=false) {
		if($flag) {
			$this->dbCriteria->with['lines_alias.property'] = array();
			$this->dbCriteria->with['uclass.properties'] = array();
		}
		else {
			unset($this->dbCriteria->with['lines_alias.property']);
			unset($this->dbCriteria->with['uclass.properties']);
		}
		$_is_force_prop = $flag;
	}
	public function status_set_force_prop() {
		return $this->_is_force_prop;
	}

	private $_tmpProperties = array();
	private $_propertiesNames = array();
	public function get_properties($force=false) {
		if(!count($this->_tmpProperties) || $force==true) {
			$arrconfcms = Yii::app()->appcms->config;
			$classproperties = $this->uclass->properties;
			$arraylinesvalue = array();
			foreach($this->lines_alias as $objline) {
				$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
				$arraylinesvalue[$objline->property->codename] = array('objline' =>$objline, 'value' => $objline->$namecolumn, 'namecol' => $namecolumn);
			}
			if(count($classproperties)) {

				foreach($classproperties as $objprop) {
					$this->_tmpProperties[$objprop->codename] = (array_key_exists($objprop->codename,$arraylinesvalue)!==false)?$arraylinesvalue[$objprop->codename]['value']:'';
				}
			}
			$this->_propertiesNames = array_keys($this->_tmpProperties);
		}
		return $this->_tmpProperties;
	}
	private function _saveProperties() {
		$classproperties = $this->uclass->properties;
		$namemodellines = str_replace('Headers','',get_class($this)).'Lines';
		$arraylinesvalue = array();
		$arrconfcms = Yii::app()->appcms->config;
		foreach($this->lines_alias as $objline) {
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
					$newobjlines->save();
					$this->UserRelated->links_edit('add','lines',array($newobjlines->primaryKey));
				}
			}
		}
	}
	private $_tempthislink;
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
		$objmodel = new $nameModelHeader();
		$objmodel->dbCriteria->addInCondition($objmodel->tableAlias.'.id', $idsheaders);
		$objmodel->dbCriteria->compare($objmodel->tableAlias.'.uclass_id',$objclass->id);
		$objmodel->uclass_id = $objclass->id;
		return $objmodel;
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
				$this->_saveProperties();
				$this->old_properties = $this->get_properties();
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
	public $_properties = array();
	public function hasProperty($name) {
		return in_array($name, $this->_propertiesNames);
	}

	public function propertyNames() {
		return $this->_propertiesNames;
	}
	public function set_properties($name,$value) {
		if(!$this->hasProperty($name)) {
			throw new CException(
				Yii::t('cms','Not find prop {prop}',
				array('{prop}'=>$name))
			);
		}
		$this->_tmpProperties[$name] = $value;
		$this->addElemClass($name.'prop_', $value);
	}
	public function __set($name, $value) {
		if($name=='attributes') {
			if(is_array($value) && count($value)) {
				foreach($value as $key => $val) {
					if(($pos = strpos($key,'prop_'))!==false) {
						$this->set_properties(substr($key,0,$pos),$val);
					}
				}
			}
		}
		elseif(($pos = strpos($name,'prop_'))!==false) {
			$propName = substr($name,0,$pos);
			$this->set_properties($propName, $value);
		}
		//можно добавить еще свои типы

		parent::__set($name, $value);
	}

	public function dinamicModel() {
		//добавляем свойтсва к модели
		if($currentproperties = $this->get_properties()) {
			$arrconfcms = Yii::app()->appcms->config;
			foreach($this->uclass->properties as $prop) {
				$nameelem = $prop->codename.'prop_';
				//инициализируем свойство
				$this->$nameelem = $currentproperties[$prop->codename];
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
	protected $old_properties=null;
	public function afterFind() {
		$this->old_properties = $this->get_properties();
		$this->dinamicModel();
	}
}
