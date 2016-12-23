<?php

class RangeValidator extends CRangeValidator {

	protected function validateAttribute($object,$attribute)
	{
		$value = $object->$attribute;
		if (is_array($value)) {
			foreach($value as $val) {
				$object->$attribute = $val;
				parent::validateAttribute($object, $attribute);
			}
			$object->$attribute = $value;
		} else {
			parent::validateAttribute($object, $attribute);
		}
	}
}

