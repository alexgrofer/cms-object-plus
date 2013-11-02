<?php
class LoginForm extends CFormModel
{
	public $login;
	public $password;
	public $rememberMe;

	private $_identity;

	public function customRules()
	{
		return array(
			array('login, password', 'required'),
			array('rememberMe', 'boolean'),
			array('password', 'authenticate'),
		);
	}
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'Remember me next time',
		);
	}
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->login,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect login or password.');
		}
	}
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->login,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}

$model=new LoginForm;

if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
{
	echo CActiveForm::validate($model);
	Yii::app()->end();
}

if(isset($_POST['LoginForm']))
{
	$model->attributes=$_POST['LoginForm'];
	if($model->validate() && $model->login()) {
		$this->redirect(Yii::app()->request->getUrlReferrer());
	}
}


?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	//'enableAjaxValidation'=>true,
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

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">

		<?php echo CHtml::submitButton('Login'); ?>
	</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->