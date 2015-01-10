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
			$this->_identity=new UserIdentity($this->login,$this->password);
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
<?php echo CHtml::errorSummary($model,'<div class="alert alert-danger">','</p>'); ?>

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
	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>
	<?php echo CHtml::submitButton(); ?>

<?php $this->endWidget(); ?>
</div>