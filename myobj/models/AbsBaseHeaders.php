<?php
abstract class AbsBaseHeaders extends CActiveRecord // (Django) class AbsBaseHeaders(models.Model):
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
    //SELF table links
    /*
    add new relation
    public function relations() {
        $arr_relation = parent::relations();
        $arr_relation['newrel'] = array rel
    }
    */
    //user
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
    public function order_cols_model($arrorder) {
        foreach($arrorder as $arpropelem) {
            $typf = (count($arpropelem)==2)?$arpropelem[1]:'asc';
            $this->dbCriteria->order .= (($this->dbCriteria->order)?',':'').($this->tableAlias).'.'.$arpropelem[0].' '.$typf;
        }
    }
    public function setuiprop($array) {
        // $array=array('condition'=>array(array('p1','<=','23'), 'or', array('p2','=','val')),'select'=>array('*' | ['p1','p2','p3']),'order'=>array(array('p1','desc')[,array('p1')]?))
        $arrconfcms = UCms::getInstance()->config;
        if(array_key_exists('condition',$array) && count($array['condition'])) {
            $textsql = '';
            foreach($array['condition'] as $cond) {
                if(is_array($cond)) {
                    $textsql .= "(lines.".$arrconfcms['TYPES_COLUMNS'][$this->getallprop()[$cond[0]]->myfield]." ".$cond[1]." ".$cond[2]." AND property.codename='".$cond[0]."')";
                }
                else {
                    $textsql .= ' '.$cond.' ';
                }
            }
            $this->dbCriteria->with['lines'] = array('with' => 'property', 'condition'=>$textsql);
            $this->dbCriteria->with['uclass.properties'] = array();
            
        }
        if(array_key_exists('order',$array) && count($array['order'])) {
            $this->dbCriteria->with['lines_alias'] = array();
            $array['order'] = array(array('intst222', 'desc'), array('st1', 'asc'));
            
            foreach($array['order'] as $arpropelem) {
                $this->dbCriteria->with['lines_order'] = array('on'=>"lines_order.property_id=".$this->getallprop()[$arpropelem[0]]->id);
                $typf = (count($arpropelem)==2)?$arpropelem[1]:'asc';
                $typeprop = $arrconfcms['TYPES_COLUMNS'][$this->getallprop()[$arpropelem[0]]->myfield];
                $this->dbCriteria->order .= (($this->dbCriteria->order)?',':'').'(case when lines_order.'.$typeprop.' is null then 1 else 0 end) asc, lines_order.'.$typeprop.' '.$typf;
            }
        }
        return $this;
    }
    private $_prev_save_prop = array();
    public function set_properties($name, $value) {
        $this->_prev_save_prop[$name] = $value;
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
            if(array_key_exists($objprop->codename, $this->_prev_save_prop)!==false) {
                if(array_key_exists($objprop->codename,$arraylinesvalue)!==false) {
                    $arraylinesvalue[$objprop->codename]['objline']->$arraylinesvalue[$objprop->codename]['namecol'] = $this->_prev_save_prop[$objprop->codename];
                    $arraylinesvalue[$objprop->codename]['objline']->save();
                }
                else {
                    $newobjlines = new $namemodellines();
                    $namecolumn = $arrconfcms['TYPES_COLUMNS'][$objprop->myfield];
                    $newobjlines->$namecolumn = $this->_prev_save_prop[$objprop->codename];
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
            throw new CException(Yii::t('cms','Not find link id {idlink}, Class "{class}"',
            array('{class}'=>$class->name, '{idlink}'=>implode(',',$idsheaders))));
        }
        
        $objectcurrentlink->UserRelated->links_edit($type,'links',$linksobjects);
    }
    public function getobjlinks($class) {
        $objectcurrentlink = $this->_getobjectlink();
        $CRITERIA = new CDbCriteria();
        $objclass = \uClasses::getclass($class);
        //проверить вернул ли класс, а то не поймет что за ошибка была даже если выскочит
        //сделать путь для сообщений cms-ки, будут ли работать yii
        //throw new CException(Yii::t('cms','Property "{class}.{property}" is not defined.',
            //array('{class}'=>get_class($this), '{property}'=>$name)));
        $idsheaders = apicms\utils\arrvaluesmodel($objectcurrentlink->links,'idobj');
        $CRITERIA->addInCondition('id', $idsheaders);
        $CRITERIA->compare('uclass_id',$objclass->id);
        $nameModelHeader = UCms::getInstance()->config['spacescl'][$objclass->tablespace]['namemodel'];
        $objmodel = new $nameModelHeader();
        $objmodel->setDbCriteria($CRITERIA);
        
        return $objmodel;
    }
    public function afterSave() {
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
        //setproperties
        if(count($this->_prev_save_prop)) {
            $this->_f_prev_save_prop();
        }
    }
    function afterDelete() {
        //del lines
        if($this->isitlines == true && count($this->lines)) {
            $idslines = apicms\utils\arrvaluesmodel($this->lines, 'id');
            $CRITERIA = new CDbCriteria();
            $CRITERIA->addInCondition('id', $idslines);
            $this->lines[0]->model()->deleteAll($CRITERIA);
        }
        //del links
        $objectcurrentlink = $this->_getobjectlink();
        if($objectcurrentlink) {
            if(count($objectcurrentlink->links)) {
                $objectcurrentlink->UserRelated->links_edit('clear','links');
            }
            $objectcurrentlink->delete();
        }
    }
    
    public function behaviors()
    {
        return array(
            'UserRelated'=>array(
                'class'=>'ext.behaviors.model.RelatedBehavior',
            ),
            'UserFormModel'=>array(
                'class'=>'application.modules.myobj.extensions.behaviors.model.FormModel',
            ),
        );
    }

}
