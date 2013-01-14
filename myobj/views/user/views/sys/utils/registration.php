<?php
$model=new User;


print_r(Yii::app()->user);
if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
{
    echo CActiveForm::validate($model);
    Yii::app()->end();
}

if(isset($_POST['User'])) {
    $model->attributes=$_POST['User'];
    $model->password = md5($_POST['User']['password']);
    
    if($model->validate()) {
        $model->save();
        $this->redirect(Yii::app()->request->getUrlReferrer());
    }
}


?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'User',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <div class="row">
        <?php echo $form->labelEx($model,'login'); ?>
        <?php echo $form->textField($model,'login'); ?>
        <?php echo $form->error($model,'login'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'password'); ?>
        <?php echo $form->passwordField($model,'password'); ?>
        <?php echo $form->error($model,'password'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Login'); ?>
    </div>

    <?php $this->endWidget(); ?>
    </div><!-- form -->