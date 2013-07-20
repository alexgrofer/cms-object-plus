<?php
abstract class AbsBaseLinksObjects extends AbsModel // (Django) class AbsBaseLinksObjects(models.Model):
{
    public $idobj; //models.IntegerField(blank=False)
    //public $name; //models.CharField(max_length=255)
    
    public function relations()
    {
        $namestrthisclass = get_class($this);
        return array(
            'links'=>array(self::MANY_MANY, $namestrthisclass,'setcms_'.strtolower($namestrthisclass).'_links(from_self_id, to_self_id)'), // links = models.ManyToManyField("self",blank=True)
        );
    }

    function beforeDelete() {
        $this->UserRelated->links_edit('clear','links');
        return parent::beforeDelete();
    }
}