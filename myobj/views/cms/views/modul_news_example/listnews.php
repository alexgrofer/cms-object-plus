<?php
$this->pageTitle='list news';
//хлебный крошки
$this->breadcrumbs=array(
	'list news',
);
?>
<h4>list news</h4>

<p><a href="<?php echo $this->createUrl($this->dicturls['paramslist'][0],array('flag_all'=>1))?>">all news</a></p>

<?php
if(Yii::app()->request->getQuery('flag_all')) {
	//получим все новости без условий
	$objClass_news_objects = uClasses::getclass('news_example')->objects();
	//важный фактор для того что бы не потерять условия нужно использовать перед каждым вызовом find, findAll, Count - если это необходимо
	$get_criteria = $objClass_news_objects->getDbCriteria();

	$COUNT_P = $objClass_news_objects->count();
}
else {

//получить все объекты класса раздела "news_section_example" новостей
$objClass_news_section_example = uClasses::getclass('news_section_example')->objects();
if(!$objClass_news_section_example) {
	echo 'error: none objects count class news_section_example';return;
}
//найти объекты по свойству "codename_news_section_example" = "business"
$objClass_news_section_example->setuiprop(array('condition'=>array(array('codename_news_section_example',true,'=',"'business'",''))));
//вытащить экземпляр искомого объекта
$objClass_news_section_example_objectBusiness = $objClass_news_section_example->find(); //если будет несколько использовать
//получить все привязанные объекты класса news
if(!$objClass_news_section_example_objectBusiness) {
	echo 'error: none objects news_section_example codename_news_section_example=business';return;
}
$objClass_news_objects = $objClass_news_section_example_objectBusiness->getobjlinks('news_example');
//важный фактор для того что бы не потерять условия нужно использовать перед каждым вызовом find, findAll, Count - если это необходимо
$get_criteria = $objClass_news_objects->getDbCriteria();
//найти объекты новостей по свойству "text_news_example" в котором встречается слово "alex"
$objClass_news_objects->setuiprop(array('condition'=>array(array('text_news_example',true,'LIKE',"'%a%'",'or'),array('annotation_news_example',true,'LIKE',"'%a%'"))),$get_criteria);
//И параметр "name" модели должен включать слово "news"
//для обычных колонок модели необходимо задавать псевдонимы таблиц при поиске
$objClass_news_objects->setuiprop(array('condition'=>array(array($objClass_news_objects->tableAlias.'.'.'name',false,'LIKE',"'%f%'",''))),$get_criteria);//проверить только с ним task
//показать общее колличество найденных объектов

$COUNT_P = $objClass_news_objects->count();
echo 'count = '.$COUNT_P;
echo '<hr/>';

//установка параметра сортировки каждый раз обновляет order заново а предыдущий стирается

//для обычных колонок модели необходимо задавать псевдонимы таблиц при сортировке
$objClass_news_objects->setuiprop(array('order'=>array(array($objClass_news_objects->tableAlias.'.'.'name','asc',false))),$get_criteria); //работает
//двойная сортировка о свойству и параметру не работает, в приоретете параметр!!


//отсортировать по свойству text_news_example (для работы со свойствами лучше использовать метод setuiprop)
$objClass_news_objects->setuiprop(array('order'=>array(array('text_news_example','desc',true))),$get_criteria); //так же может учавствовать вместе с condition и select
//1)Найти что мне мешает показать нужные элементы

}

// go PAGE
$COUNTVIEWELEMS = 1;
$COUNTVIEWPAGES = 10;

$idpage = Yii::app()->request->getQuery('idpage',0); //по умолчанию 0

if($idpage==1) $idpage=0;
elseif($idpage!=0) $idpage -= 1;
if($COUNT_P > $COUNTVIEWELEMS) {
	$objClass_news_objects->setuiprop(array('limit'=>array('limit'=>$COUNTVIEWELEMS,'offset'=>$COUNTVIEWELEMS * $idpage)),$get_criteria);
}
// to PAGE

//показать SELECT в табличном виде включая свойства text_news_example и annotation_news
$array_objClass_news_objects = $objClass_news_objects->findAll($get_criteria);
echo '<table border="1"><tr><td>id</td><td>name</td><td>annotation_news</td><td>text_news_example</td></tr>';
$v = 1;
foreach($array_objClass_news_objects as $obj) {
	$properties = $obj->uProperties;
	echo '<tr><td>'.$v.'--'.$obj->primaryKey.'</td><td>'.$obj->name.'</td><td>'.$properties['annotation_news_example'].'</td><td>'.$properties['text_news_example'].'</td>
	<td><a href="'.$this->createUrl('news_edit_object_example/edit/'.$obj->primaryKey).'">редактировать</a></td>
	</tr>';
	$v++;
}
echo '</table>';

if($COUNT_P>$COUNTVIEWELEMS) {
$allGetParams = array_merge($_GET,array('idpage'=>'T_ID_PAGE'));
//normal url
$tamplate = array(
	'action'=>' class="active"',
	'nextleft'=>'<li><a href="'.$this->createUrl($this->dicturls['paramslist'][0],$allGetParams).'">&laquo;</a></li>',
	'prevpg'=>'<li class="previous"><a id="prevpg" href="'.$this->createUrl($this->dicturls['paramslist'][0],$allGetParams).'">&larr;</a></li>',
	'nextpg'=>'<li class="next"><a id="nextpg" href="'.$this->createUrl($this->dicturls['paramslist'][0],$allGetParams).'"> &rarr;</a></li>',
	'nextright'=>'<li><a href="'.$this->createUrl($this->dicturls['paramslist'][0],$allGetParams).'">&raquo;</a></li>',
	'elem'=>'<li%s><a href="'.$this->createUrl($this->dicturls['paramslist'][0],$allGetParams).'">%s</a></li>',
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