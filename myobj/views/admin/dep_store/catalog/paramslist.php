<?php
echo $str_menu_link;

echo '<div class="div"><a class="btn" href="'.$this->apcms->geturlpage('storedep_option','params/'.$this->dicturls['paramslist'][3].'/edit/0').'">create new</a></div>';

?>
<table class="table table-striped table-bordered table-condensed">
<thead>
<tr>
<?php
echo '<th>id</th>';
echo '<th>val</th>';
echo '<th>ui</th>';
?>
</tr>
</thead>
<tbody>
<?php
foreach(Option::getParams($this->dicturls['paramslist'][3]) as $value) {
    echo '<tr>';
    echo '<td>'.$value['id'].'</td>';
    echo '<td>'.$value['val'].'</td>';
    echo '<td><a title="remove" onclick="return confirm(\'remove id - '.$value['id'].'\')" href="'.$this->apcms->geturlpage('storedep_option','remove/'.$value['id']).'"><i class="icon-remove"></i></a></td>';
    echo '</tr>';
}
?>
</tbody>
</table>