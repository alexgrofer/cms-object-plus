<?php
abstract class AbsBaseHeaders extends AbsModel // (Django) class AbsBaseHeaders(models.Model):
{
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	protected function getNameLinksModel() {
		return UCms::getInstance()->config['spacescl'][$this->uclass->tablespace]['namelinksmodel'];
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
		$this->properties[$name] = $value;
	}
	private $_propertiesdict = array();
	public function get_properties($force=false) {
		if(!count($this->_propertiesdict) || $force==true) {
			$arrconfcms = UCms::getInstance()->config;
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
		$arrconfcms = UCms::getInstance()->config;
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
					$this->UserRelated->links_edit('add','lines',$newobjlines);
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

		$objectcurrentlink->UserRelated->links_edit($type,'links',$linksobjects);
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
		$nameModelHeader = UCms::getInstance()->config['spacescl'][$objclass->tablespace]['namemodel'];
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
		if(isset(UCms::getInstance()->config['controlui']['none_del']['objects'][$this->uclass->codename])) {
			foreach(UCms::getInstance()->config['controlui']['none_del']['objects'][$this->uclass->codename] as $key => $val) {
				if($this->$key==$val) return false;
			}
		}
		//del lines
		if($this->isitlines == true && count($this->lines)) {
			$this->UserRelated->links_edit('clear','lines'); //очистить ссылки на строки в дочерней тиблице
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
				$objectcurrentlink->UserRelated->links_edit('clear','links'); //очистить ссылки на *ссылки в дочерней тиблице
			}
			$objectcurrentlink->delete(); //удалить ссылку
		}
		return parent::beforeDelete();
	}
}
