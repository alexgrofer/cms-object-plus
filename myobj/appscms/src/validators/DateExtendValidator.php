<?php

class DateExtendValidator extends CDateValidator
{

	/*
	protected function validateAttribute($object,$attribute) {
		$value=$object->$attribute;
		if (CDateTimeParser::parse($value,$this->format)==false) {
			$this->addError($object, $attribute, $this->message);
		}
	}
	*/

	public function clientValidateAttribute($object,$attribute) {
		return "if(1){messages.push(".CJSON::encode($this->message).");}";
	}
}

