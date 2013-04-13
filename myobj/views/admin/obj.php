<?php
if(!$REND_acces_write) {
    //должно делаться в контроллере - или добавить мини контроллер ?
    echo '<p class="alert">not acces edit</p>';
}
$htmldiv = '<div%s>%s</div>';
$htmlp='<p class="%s">%s</p>';
$htmlspan='<span class="%s">%s</span>';
$htmlinput='<input type="%s" name="%s" value="%s" class="%s" />';
$form = $REND_model->UserFormModel->initform($_POST,$REND_editform);
$form->attributes = array('enctype' => 'multipart/form-data');
echo $form;

if(count($_POST) && $form->validate()) {
    $REND_model->save();
    if($this->dicturls['actionid']=='0') {
        $this->redirect($this->getUrlBeforeAction());
    }
    else {
        $this->redirect(Yii::app()->request->url);
    }
}

?>
<style>
.errorMessage {color: red;padding-bottom: 15px}
</style>