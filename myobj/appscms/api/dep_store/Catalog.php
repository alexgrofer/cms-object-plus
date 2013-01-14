<?php
Yii::import('application.modules.myobj.library.dep_store.AbsCatalog');
class Catalog extends AbsCatalog {
    public static function get($filterarr=array(),$limit=array('offset'=>0,'limit'=>100)) {
        //$filterarr=array('id'=>null,'name'=>null,'top'=>null,'guid'=>null)
        //FACADE START
        $modelCatalog = DepstoreCatalog::model();
        
        $arr = $modelCatalog->findAll();
        return $arr;
        //FACADE END
    }
    public static function edit_creat($p_array,$id) {
        //FACADE START
        $objCatalog = DepstoreCatalog::model()->findByPk($id) ?: (new DepstoreCatalog());
        foreach($p_array as $key => $val) {
                if(in_array($key,static::$nameparams)) {
                    $objCatalog->$key = $val;
                }
        }
        $objCatalog->save();
        //FACADE END
    }
    public static function del($id) {
        //FACADE START
        DepstoreCatalog::model()->deleteByPk($id);
        //FACADE END
    }
}
?>