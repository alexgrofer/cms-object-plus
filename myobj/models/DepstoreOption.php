<?php
class DepstoreOption extends CActiveRecord
{
    public $name;
    public $type;
    public $exp;
    public $range;
    public function gettypeAr() {
        return array('1'=>'val','2'=>'bool','3'=>'select');
    }
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    public function rules() {
        return array(
            array('name, type', 'required'),
            array('exp, range', 'default', 'value'=>''),
        );
    }
    public function relations()
    {
        return array(
            'params'=>array(self::HAS_MANY, 'DepstoreOptionParams', 'id_option'),
        );
    }
    public function ElementsForm() {
        return array(
            'name'=>array(
                'type'=>'text',
            ),
            'type'=>array(
                'type'=>'dropdownlist',
                'items'=>$this->gettypeAr(),
            ),
            'exp'=>array(
                'type'=>'text',
            ),
            'range'=>array(
                'type'=>'text',
            ),
        );
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