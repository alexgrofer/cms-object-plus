<?php

class ClientDateValidator extends CDateValidator {
	public function clientValidateAttribute($object,$attribute) {
		return "
		function isValidDate(date) {
			var matches = date.split('-');
			if(matches[0].length==4) {
				var y = parseInt(matches[0]);
				var m = parseInt(matches[1]);
				var d = parseInt(matches[2]);
			}
			else {
				var d = parseInt(matches[0]);
				var m = parseInt(matches[1]);
				var y = parseInt(matches[2]);
			}
			m -= 1;
			var composedDate = new Date(y, m, d);
			return composedDate.getDate() == d &&
				composedDate.getMonth() == m &&
				composedDate.getFullYear() == y;
		}
		if(isValidDate(value)==false) {messages.push(".CJSON::encode($this->message).");}";
	}
}

