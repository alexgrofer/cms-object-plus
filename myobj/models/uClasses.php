<?php
class uClasses extends AbsModel
{
    public $name; //models.CharField(max_length=255)
    public $codename; //models.CharField(max_length=30,unique=True)
    public $description; //models.CharField(max_length=255,blank=True)
    public $tablespace; //models.PositiveSmallIntegerField(choices=MYCONF.MYSPACE_TABLES_CHOICES, default=1)
    
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    
    public function relations()
    {
        return array(
            'properties'=>array(self::MANY_MANY, 'objProperties', 'setcms_uclasses_objproperties(from_uclasses_id, to_objproperties_id)'), //properties = models.ManyToManyField(objProperties,blank=True)
            'association'=>array(self::MANY_MANY, 'uClasses', 'setcms_uclasses_association(from_uclasses_id, to_uclasses_id)'), //association = models.ManyToManyField("self",blank=True))
			//'objectCount'=>array(self::STAT, 'myObjHeaders', 'uclass_id'),
        );
    }
    public function rules()
    {
        return array(
            array('name,  codename', 'required'),
            array('name', 'length', 'max'=>255),
            array('codename', 'length', 'max'=>30),
            array('codename', 'unique'),
            array('tablespace', 'default', 'value'=>1),
            array('description', 'default', 'value'=>''),
            array('tablespace', 'numerical'),
            
        );
    }
	public function getobjectCount() {
		return $this->objects()->count();
	}
	//именно перед удалением beforeDelete - удалить объекты класса Перед его удалением, иначе невозможно будет узнать параметры класса при их удалении
    public function beforeDelete() {
        //delete our objects
        foreach($this->objects()->findAll() as $obj) {
            $obj->delete();
        }
        return parent::beforeDelete();
    }
    public function getTSPACESOptions(){
        $oprion = array();
        foreach(UCms::getInstance()->config['spacescl'] as $key => $value) {
            $oprion[$key] = $value['namemodel'];
        }
        return $oprion;
    }
    public function attributeLabels() {
        return array(
            'name' => 'name',
            'codename' => 'code name',
            'description' => 'description',
            'tablespace' => 'table space',
       );
    }
    public function ElementsForm() {
        return array(
            'name'=>array(
                'type'=>'text',
            ),
            'codename'=>array(
                'type'=>'text',
            ),
            'description'=>array(
                'type'=>'text',
            ),
            'tablespace'=>array(
                'type'=>'dropdownlist',
                'items'=>$this->getTSPACESOptions(),
            ),
        );
    }
    //user func Object class
    //comment doc
    public function initobject() { //newobj = $class->initobject(); $newobj.save()
        $modelheaders = $this->objects();
        $newobj = new $modelheaders();
        $newobj->uclass_id = $this->id;
        return $newobj;
    }
    //comment doc
    public function objects() { //namesvprop left join props lines
        $NameClassHeader = UCms::getInstance()->config['spacescl'][$this->tablespace]['namemodel'];
        $modelheaders = new $NameClassHeader();
        $modelheaders->dbCriteria->compare('uclass_id',$this->id);
        $modelheaders->dbCriteria->select = array('t.*');
        
        return $modelheaders;
    }
    //user func classes
    //comment doc
	//поиск класса по codename или id
    static function getclass($classidorname) {
        $papamfind = ((int)$classidorname)?'id':'codename';
        if(is_array($classidorname)) {
            $papamfind = ((int)$classidorname[0])?'id':'codename';
            $objclass = uClasses::model()->findAllByAttributes(array($papamfind => $classidorname));
            $keysarray = array();
            foreach($objclass as $objclass) {
                $keysarray[$objclass->codename] = $objclass;
            }
            if(count($keysarray)) {
                $objclass = $keysarray;
            }
        }
        else {
            $objclass = uClasses::model()->findByAttributes(array($papamfind => $classidorname));
        }
        return $objclass;
    }
}

