<h4>list news</h4>
<?php
//получить все объекты класса раздела "news_section" новостей
$objClass_news_section = uClasses::getclass('news_section')->objects();
//найти объекты по свойству "codename_news_section" = "business"
$objClass_news_section->setuiprop(array('condition'=>array(array('codename_news_section',true,'=',"'business'",''))));
//вытащить экземпляр искомого объекта
$objClass_news_section_objectBusiness = $objClass_news_section->find(); //если будет несколько использовать
//получить все привязанные объекты класса news

$objClass_news_objects = $objClass_news_section_objectBusiness->getobjlinks('news');
//важный фактор для того что бы не потерять условия нужно использовать перед каждым вызовом find, findAll, Count - если это необходимо
$get_criteria = $objClass_news_objects->getDbCriteria();
//найти объекты новостей по свойству "text_news" в котором встречается слово "alex"
$objClass_news_objects->setuiprop(array('condition'=>array(array('text_news',true,'LIKE',"'%a%'",'or'),array('annotation_news',true,'LIKE',"'%a%'"))),$get_criteria);
//И параметр "name" модели должен включать слово "news"
$objClass_news_objects->setuiprop(array('condition'=>array(array('name',false,'LIKE',"'%f%'",''))),$get_criteria);//проверить только с ним task
//показать общее колличество найденных объектов

$COUNT_P = $objClass_news_objects->count();
echo 'count = '.$COUNT_P;
echo '<hr/>';

//отсортировать по свойству параметру id (для работы с параметрами лучше использовать dbCriteria)
$objClass_news_objects->setuiprop(array('order'=>array(array('name','asc',false))),$get_criteria); //работает
//двойная сортировка о свойству и параметру не работает, в приоретете параметр!!

//отсортировать по свойству text_news (для работы со свойствами лучше использовать метод setuiprop)
$objClass_news_objects->setuiprop(array('order'=>array(array('text_news','desc',true))),$get_criteria); //так же может учавствовать вместе с condition и select
//1)Найти что мне мешает показать нужные элементы



//использовать метод set_force_prop(true); для того что бы ограничить колличество запросов при поиске строк свойств (так как в выводе будут присутствовать свойства)
$objClass_news_objects->set_force_prop(true);

// go PAGE
$COUNTVIEWELEMS = 31;
$COUNTVIEWPAGES = 10;
$idpage = 0;
if(strpos(implode('',array_keys($_GET)),'goin_')!==false) {
    foreach($_GET as $key => $value) {
        if($key == 'goin_') {
            $idpage = $value;
        }
    }
}
elseif(array_key_exists('idpage',$_POST)) $idpage = $_POST['idpage'];

if($idpage==1) $idpage=0;
elseif($idpage!=0) $idpage -= 1;
if($COUNT_P > $COUNTVIEWELEMS) {
    $objClass_news_objects->setuiprop(array('limit'=>array('limit'=>$COUNTVIEWELEMS,'offset'=>$COUNTVIEWELEMS * $idpage)),$get_criteria);
}
// to PAGE

//показать SELECT в табличном виде включая свойства text_news и annotation_news
$array_objClass_news_objects = $objClass_news_objects->findAll($get_criteria);
echo '<table border="1"><tr><td>id</td><td>name</td><td>annotation_news</td><td>text_news</td></tr>';
$v = 1;
foreach($array_objClass_news_objects as $obj) {
    $properties = $obj->get_properties();
    echo '<tr><td>'.$v.'--'.$obj->id.'</td><td>'.$obj->name.'</td><td>'.$properties['annotation_news'].'</td><td>'.$properties['text_news'].'</td></tr>';
    $v++;
}
echo '</table>';

if($COUNT_P>$COUNTVIEWELEMS) {
//normal url
$tamplate = array(
    'action'=>' class="active"',
    'nextleft'=>'<li><a href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&laquo;</a></li>',
    'prevpg'=>'<li class="previous"><a id="prevpg" href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&larr;</a></li>',
    'nextpg'=>'<li class="next"><a id="nextpg" href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s"> &rarr;</a></li>',
    'nextright'=>'<li><a href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&raquo;</a></li>',
    'elem'=>'<li%s><a href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">%s</a></li>',
    'pagination' => '
        <div id="pagination" class="pagination">
            <ul class="pager">
                %s
            </ul>
            <p class="pagin-lenks"><ul>%s</ul></p>
        </div>
<script>
$(document).keydown(function(event){if(event.ctrlKey){if(event.keyCode == 37){if($("#prevpg").length){$("#prevpg")[0].click()}}else if(event.keyCode == 39){if($("#nextpg").length){$("#nextpg")[0].click()}}}});
</script>
');

echo '<div style="padding-bottom: 60px">'.apicms\utils\pagination($idpage,$COUNT_P,$COUNTVIEWELEMS,$COUNTVIEWPAGES,'',true,$tamplate).'</div>';
}
?>