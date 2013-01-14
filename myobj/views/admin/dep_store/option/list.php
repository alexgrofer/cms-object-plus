<?php
echo $str_menu_link;

echo '<div class="div"><a class="btn" href="'.$this->apcms->geturlpage('storedep_option','edit/0').'">create new</a></div>';

?>
<table class="table table-striped table-bordered table-condensed">
<thead>
<tr>
<?php

echo '<th>id</th>';
foreach(Option::getNameparams() as $key => $value) {
    echo '<th>'.$value.'</th>';
}
echo '<th>ui</th>';
?>
</tr>
</thead>
<tbody>
<?php
foreach(Option::get() as $value) {
    echo '<tr>';
    echo '<td>'.$value['id'].'</td>';
    foreach(Option::getNameparams() as $colname) {
        echo '<td>'.$value[$colname].'</td>';
    }
    echo '<td><a title="link params" href="'.$this->apcms->geturlpage('storedep_option','params/'.$value['id']).'"><i class="icon-resize-small"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a title="edit" href="'.$this->apcms->geturlpage('storedep_option','edit/'.$value['id']).'"><i class="icon-edit"></i></a> | --- <a title="remove" onclick="return confirm(\'remove id - '.$value['id'].'\')" href="'.$this->apcms->geturlpage('storedep_option','remove/'.$value['id']).'"><i class="icon-remove"></i></a></td>';
    echo '</tr>';
}
?>
</tbody>
</table>