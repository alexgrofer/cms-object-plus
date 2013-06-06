<h4>list news</h4>
<?php
//получить все объекты класса раздела "news_section" новостей
$objClass_news_section = uClasses::getclass('news_section')->objects();
//найти объекты по свойству "codename_news_section" = "business"
$objClass_news_section->setuiprop(array('condition'=>array(array('codename_news_section','=',"'business'"))));
//вытащить экземпляр объекта
$objClass_news_section_objectBusiness = $objClass_news_section->find();
//получить все привязанные объекты класса news
$objClass_news_objects = $objClass_news_section_objectBusiness->getobjlinks('news');
//найти объекты новостей по свойству "codename_news_section" = "business"

//показать общее колличество объектов
echo $objClass_news_objects->count();
echo '<hr/>';

//отсортировать по свойству text_news
//или отсортировать по свойству параметру id
//использовать метод set_force_prop(true); для того что бы ограничить колличество запросов при поиске строк свойств (так как в выводе будут присутствовать свойства)
//показать в табличном виде включая свойства text_news и annotation_news, 2)показать не указывая в select свойства annotation_news
    //annotation_news не должен быть виден в колонке - проверить передаваиваемую память

?>