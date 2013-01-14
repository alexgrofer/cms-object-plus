<?php
echo '<a class="btn" href="'.$this->apcms->geturlpage('storedep_catalog','edit/0').'">create new</a><br/>';
?>
<table class="table table-striped table-bordered table-condensed">
<thead>
<tr>
<?php
/*
+сделать древовидную структуру
*/


echo '<th>id</th>';
foreach(Catalog::getNameparams() as $key => $value) {
    echo '<th>'.$value.'</th>';
}
echo '<th>ui</th>';
?>
</tr>
</thead>
<tbody>
<?php
foreach(Catalog::get() as $value) {
    echo '<tr>';
    echo '<td>'.$value['id'].'</td>';
    foreach(Catalog::getNameparams() as $colname) {
        echo '<td>'.$value[$colname].'</td>';
    }
    echo '<td><a href="'.$this->apcms->geturlpage('storedep_catalog','edit/'.$value['id']).'"><i class="icon-edit"></i></a> | --- <a  onclick="return confirm(\'remove id - '.$value['id'].'\')" href="'.$this->apcms->geturlpage('storedep_catalog','remove/'.$value['id']).'"><i class="icon-remove"></i></a></td>';
    echo '</tr>';
}
?>
</tbody>
</table>