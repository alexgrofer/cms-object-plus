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

	public $setproperties = array();
	private $_allproperties = array();
	public function getallprop() {
		if(!$this->_allproperties) {
			foreach(objProperties::model()->findAll() as $prop) {
				$this->_allproperties[$prop->codename] = $prop;
			}
		}
		return $this->_allproperties;
	}
	public $properties = array();
	//метод позволяет установить новое свойство для объекта
	public function set_properties($name, $value) {
		//этот хук необходим для адекватной обработки rules
		$nameelem = $name.'prop_';
		$this->$nameelem = $value;

		$this->properties[$name] = $value;
	}
	private $_propertiesdict = array();
	public function get_properties($force=false) {
		if(!count($this->_propertiesdict) || $force==true) {
			$arrconfcms = Yii::app()->appcms->config;
			$classproperties = $this->uclass->properties;
			$arraylinesvalue = array();
			foreach($this->lines_alias as $objline) {
				$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
				$arraylinesvalue[$objline->property->codename] = array('objline' =>$objline, 'value' => $objline->$namecolumn, 'namecol' => $namecolumn);
			}
			if(count($classproperties)) {

				foreach($classproperties as $objprop) {
					$this->_propertiesdict[$objprop->codename] = (array_key_exists($objprop->codename,$arraylinesvalue)!==false)?$arraylinesvalue[$objprop->codename]['value']:'';
				}
			}
		}
		return $this->_propertiesdict;
	}
	private function _f_prev_save_prop() {
		$classproperties = $this->uclass->properties;
		$namemodellines = str_replace('Headers','',get_class($this)).'Lines';
		$arraylinesvalue = array();
		$arrconfcms = Yii::app()->appcms->config;
		foreach($this->lines_alias as $objline) {
			$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objline->property->myfield];
			$arraylinesvalue[$objline->property->codename] = array('objline' =>$objline, 'value' => $objline->$namecolumn, 'namecol' => $namecolumn);
		}
		foreach($classproperties as $objprop) {
			if(array_key_exists($objprop->codename, $this->properties)!==false) {
				if(array_key_exists($objprop->codename,$arraylinesvalue)!==false) {
					$arraylinesvalue[$objprop->codename]['objline']->$arraylinesvalue[$objprop->codename]['namecol'] = $this->properties[$objprop->codename];
					$arraylinesvalue[$objprop->codename]['objline']->save();
				}
				else {
					$newobjlines = new $namemodellines();
					$namecolumn = $arrconfcms['TYPES_COLUMNS'][$objprop->myfield];
					$newobjlines->$namecolumn = $this->properties[$objprop->codename];
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
			if(count($this->properties)) {
				$this->_f_prev_save_prop();
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
	public $costRules=array();

	protected $costElementsForm=array();
	public function rules() {
		if(method_exists(get_class($this),'get_properties')) {
			$arrconfcms = Yii::app()->appcms->config;
			$currentproperties = $this->get_properties();
			foreach($this->uclass->properties as $prop) {
				$nameelem = $prop->codename.'prop_';
				$this->$nameelem = '';
				if(array_key_exists('EmptyForm',$_POST) && array_key_exists($nameelem, $_POST['EmptyForm'])) {
					$this->$nameelem = $_POST['EmptyForm'][$nameelem];
				}
				else {
					$this->$nameelem = $currentproperties[$prop->codename];
				}

				if($prop->minfield) $this->costRules[] = array($nameelem, 'length', 'min'=>$prop->minfield);
				if($prop->maxfield) $this->costRules[] = array($nameelem, 'length', 'max'=>$prop->maxfield);
				if($prop->required) $this->costRules[] = array($nameelem, 'required');
				if($prop->udefault) $this->costRules[] = array($nameelem, 'default', 'value'=>$prop->udefault);

				$nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];
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
					$this->costRules[] = $addarrsett;
				}
				//для остальных нужно прописать safe иначе не будут отображаться в редактировании объекта
				else {
					$this->costRules[] = array($nameelem, 'safe');
				}
				if($nametypef=='bool') $this->costRules[] = array($nameelem, 'boolean');
				if($nametypef=='url') $this->costRules[] = array($nameelem, 'url');
				if($nametypef=='email') $this->costRules[] = array($nameelem, 'email');

				$nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];

				$this->costElementsForm[$nameelem] = array('type' => $arrconfcms['TYPES_MYFIELDS'][$nametypef]);
			}
			//запомнить старые значения иногда это требуется
			foreach($this->attributes as $key => $value) {
				$this->old_attributes[$key] = $value;
			}
		}
		return $this->costRules;
	}
	protected function afterValidate() {

		parent::afterValidate();

		if($this->isNewRecord && method_exists(get_class($this),'get_properties')) $this->uclass_id = $this->uclass->id;
		if(!isset($_POST['EmptyForm'])) return;
		foreach($_POST['EmptyForm'] as $key => $value) {
			//start prop
			if(($posptop = strpos($key, 'prop_'))!==false) {
				$trynameprop = substr($key,0,$posptop);
				$this->set_properties($trynameprop,$value);
			}
			//end prop
			elseif(property_exists($this, $key)) {
				$this->$key = $value;
			}
		}

	}
}
