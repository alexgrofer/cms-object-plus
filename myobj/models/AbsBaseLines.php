<?php
abstract class AbsBaseLines extends CActiveRecord // (Django) class AbsBaseLines(models.Model):
{
    
    public $uptextfield; //models.TextField(blank=True)
    public $upcharfield; //models.CharField(max_length=255,blank=True)
    public $uptimefield; //models.TimeField(blank=True,null=True)
    public $updatefield; //models.DateField(blank=True,null=True)
    public $upintegerfield; //models.IntegerField(blank=True,null=True)
    public $upfloatfield; //models.FloatField(blank=True,null=True)
    
    public function relations()
    {
        return array(
            'property'=>array(self::BELONGS_TO, 'objProperties', 'property_id'), // property = models.ForeignKey(objProperties)
        );
    }
    
    public function rules()
    {
        return array(
            array('upcharfield', 'length', 'max'=>255),
            array('uptextfield, upcharfield', 'default', 'value'=>''),
            array('uptimefield, updatefield, upintegerfield, upfloatfield', 'default', 'value'=>null),
        );
    }
    // do all the work
    public function defloadfunc($arrupload) { //$arrupload = array keys: [path,ObjCUploadedFile,funcload]
        $namefile = $arrupload['path'].$arrupload['ObjCUploadedFile']->getName();
        $arrupload['ObjCUploadedFile']->saveAs($namefile);
        $this->uptextfield = $namefile;
        return $namefile;
    }
    public function beforeSave() {
        //saves files
        if(is_array($this->uptextfield) && array_key_exists('ObjCUploadedFile',$this->uptextfield)) {
            $namefunkloader = 'defloadfunc';
            if($this->uptextfield['funcload']!='') {
                $namefunkloader = $this->uptextfield['funcload'];
            }
            $this->$namefunkloader($this->uptextfield);
        }
        return parent::beforeSave();
    }
    public function behaviors()
    {
        return array(
            'UserRelated'=>array(
                'class'=>'ext.behaviors.model.RelatedBehavior',
            ),
        );
    }
}
