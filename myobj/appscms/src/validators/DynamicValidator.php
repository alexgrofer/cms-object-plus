<?php

/**
 * Class DynamicValidator
 *
$form = \MYOBJ\appscms\core\base\form\DForm::create();
$form->addAttributeRule('p1', array('required'));
$function = function($object,$attribute) {
$object->addError('p1', 'error');
};
$form->addAttributeRule('p1', array('DynamicValidator', 'function'=>$function));
 * 
 */
class DynamicValidator extends CDateValidator {

	public $function;
	public $functionJS;

	protected function validateAttribute($object,$attribute) {
		return $this->function($object,$attribute);
	}

	public function clientValidateAttribute($object,$attribute) {
		return $this->functionJS($object,$attribute);
	}
}