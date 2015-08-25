<?php

class DateExtendValidator extends CDateValidator
{
	/**
	 * @var string
	 */
	public $patternDateJS='/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/';

	public function clientValidateAttribute($object,$attribute) {
		return "
		function isValidDate(date) {
			var matches = ".$this->patternDateJS.".exec(date);
			if(matches == null) return false;
			var d = matches[1];
			var m = matches[2] - 1;
			var y = matches[3];
			var composedDate = new Date(y, m, d);
			return composedDate.getDate() == d &&
				composedDate.getMonth() == m &&
				composedDate.getFullYear() == y;
		}
		if(isValidDate(value)==false) {messages.push(".CJSON::encode($this->message).");}";
	}
}

