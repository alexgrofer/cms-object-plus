<?php
class LoginForm extends CFormModel
{
	public $login;
	public $password;
	public $rememberMe;

	public $authenticate;

	private $_identity;

	public function rules() {
		return array(
			array('login, password', 'required'),
			array('rememberMe', 'boolean'),
		);
	}

	public function validate($attributes = NULL, $clearErrors = true) {
		$isValidate = parent::validate($attributes, $clearErrors);

		if($isValidate) {
			$this->_identity=new UserIdentityAdmin($this->login,$this->password);
			if(!$this->_identity->authenticate()) {
				$this->addError('authenticate', 'incorrect authenticate');
				return false;
			}

			if($this->_identity->errorCode===UserIdentity::ERROR_NONE) {
				$duration=$this->rememberMe==false ? 0 : 3600*24*30; // 30 days
				Yii::app()->user->login($this->_identity,$duration);
				return true;
			}
		}
		return false;
	}
}

$model=new LoginForm;

if(isset($_POST['LoginForm'])) {
	$model->attributes=$_POST['LoginForm'];
	if($model->validate()) {
		$this->redirect(Yii::app()->createUrl('myobj/admin/objects/models/classes'));
	}
}
?>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm'); ?>
<?=CHtml::errorSummary($model,'<div class="alert alert-danger">','</p>'); ?>

	<div class="row">
		<?=$form->labelEx($model,'login'); ?>
		<?=$form->textField($model,'login'); ?>
		<?=$form->error($model,'login'); ?>
	</div>
	<div class="row">
		<?=$form->labelEx($model,'password'); ?>
		<?=$form->passwordField($model,'password'); ?>
		<?=$form->error($model,'password'); ?>
	</div>
	<div class="row rememberMe">
		<?=$form->checkBox($model,'rememberMe'); ?>
		<?=$form->label($model,'rememberMe'); ?>
		<?=$form->error($model,'rememberMe'); ?>
	</div>
	<?=CHtml::submitButton(); ?>

<?php $this->endWidget(); ?>
</div>