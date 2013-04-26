<?php
abstract class AbsBaseLines extends AbsModel // (Django) class AbsBaseLines(models.Model):
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
}
