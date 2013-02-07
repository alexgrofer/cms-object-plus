<?php
echo $str_menu_link;
if($this->dicturls['paramslist'][3]) {
echo '<div class="div"><a class="btn" href="'.$this->apcms->geturlpage('storedep_catalog','params/'.$this->dicturls['paramslist'][4].'/edit/0').'">create new</a></div>';
}
?>
<table class="table table-striped table-bordered table-condensed">
<thead>
<tr>
<?php
echo '<th>id</th>';
echo '<th>val</th>';
echo '<th>id_opt</th>';
echo '<th>ui</th>';
?>
</tr>
</thead>
<tbody>
<?php
foreach(Option::getParams($this->dicturls['paramslist'][4]) as $value) {
    echo '<tr>';
    echo '<td>'.$value['id'].'</td>';
    echo '<td>'.$value['val'].'</td>';
    echo '<td>'.$value['id_option'].'</td>';
    echo '<td><a title="edit" href="'.$this->apcms->geturlpage('storedep_catalog','params/edit/'.$value['id']).'"><i class="icon-edit"></i></a> | --- <a title="remove" onclick="return confirm(\'remove id - '.$value['id'].'\')" href="'.$this->apcms->geturlpage('storedep_catalog','params/remove/'.$value['id']).'"><i class="icon-remove"></i></a></td>';
    echo '</tr>';
}
?>
</tbody>
</table>