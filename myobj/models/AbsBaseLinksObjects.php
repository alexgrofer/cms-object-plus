<?php
abstract class AbsBaseLinksObjects extends CActiveRecord // (Django) class AbsBaseLinksObjects(models.Model):
{
    public $idobj; //models.IntegerField(blank=False)
    //public $name; //models.CharField(max_length=255)
    
    public function relations()
    {
        $namestrthisclass = get_class($this);
        return array(
            'uclass'=>array(self::BELONGS_TO, 'uClasses', 'uclass_id'), // uclass = models.ForeignKey(uClasses)
            'links'=>array(self::MANY_MANY, $namestrthisclass,'setcms_'.strtolower($namestrthisclass).'_links(from_self_id, to_self_id)'), // links = models.ManyToManyField("self",blank=True)
        );
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
