<?php

class ClientDateValidator extends CValidator {
	public $patternDateJS='/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/';
	public $message='Error date';

	public function clientValidateAttribute($object,$attribute) {
		return "
		function isValidDate(date) {
			var matches = ".$this->patternDateJS.".exec(date);
			if(matches == null) return false;
			if(matches[1].length==4) {
				var y = parseInt(matches[1]);
				var m = parseInt(matches[2]);
				var d = parseInt(matches[3]);
			}
			else {
				var d = parseInt(matches[1]);
				var m = parseInt(matches[2]);
				var y = parseInt(matches[3]);
			}
			m -= 1;
			var composedDate = new Date(y, m, d);
			return composedDate.getDate() == d &&
				composedDate.getMonth() == m &&
				composedDate.getFullYear() == y;
		}
		if(isValidDate(value)==false) {messages.push(".CJSON::encode($this->message).");}";
	}

	protected function validateAttribute($object,$attribute) {
		return true;
	}
}

