<h4>list news</h4>
<?php
//получить все объекты класса раздела "news_section" новостей
$objClass_news_section = uClasses::getclass('news_section')->objects();
//найти объекты по свойству "codename_news_section" = "business"
$objClass_news_section->setuiprop(array('condition'=>array(array('codename_news_section','=',"'business'"))));
//вытащить экземпляр искомого объекта
$objClass_news_section_objectBusiness = $objClass_news_section->find(); //если будет несколько использовать
//получить все привязанные объекты класса news
$objClass_news_objects = $objClass_news_section_objectBusiness->getobjlinks('news');
//найти объекты новостей по свойству "text_news" в котором встречается слово "alex"
$objClass_news_objects->setuiprop(array('condition'=>array(array('text_news','LIKE',"'%alex%'",'or'),array('annotation_news','LIKE',"'%texddt%'"))));
//И параметр "name" модели должен включать слово "news"
$objClass_news_objects->setuiparam(array('condition'=>array(array('name','LIKE',"'%news%'"))));
//показать общее колличество найденных объектов
echo 'count = '.$objClass_news_objects->count();
echo '<hr/>';

//отсортировать по свойству text_news (для работы со свойствами лучше использовать метод setuiprop)
$objClass_news_objects->setuiprop(array('order'=>array(array('text_news','asc')))); //так же может учавствовать вместе с condition и select
//или отсортировать по свойству параметру id (для работы с параметрами лучше использовать dbCriteria)
$objClass_news_objects->setuiparam(array('order'=>array(array('id','asc'),array('name','desc'))));

//использовать метод set_force_prop(true); для того что бы ограничить колличество запросов при поиске строк свойств (так как в выводе будут присутствовать свойства)
$objClass_news_objects->set_force_prop(true);
//показать SELECT в табличном виде включая свойства text_news и annotation_news
$array_objClass_news_objects = $objClass_news_objects->findAll();
echo '<table border="1"><tr><td>id</td><td>name</td><td>annotation_news</td><td>text_news</td></tr>';
foreach($array_objClass_news_objects as $obj) {
    $properties = $obj->get_properties();
    echo '<tr><td>'.$obj->id.'</td><td>'.$obj->name.'</td><td>'.$properties['annotation_news'].'</td><td>'.$properties['text_news'].'</td></tr>';
}
echo '</table>';
?>