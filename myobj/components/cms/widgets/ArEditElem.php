<?php
/**
 * Class ArEditElem виджет для отображения одномерного массива в форме
 */
class ArEditElem extends CInputWidget
{
    public $ownerModel;

	public function run()
	{
		$confArElemKeys = $this->ownerModel->getConfArElemKeys();
		$nameElem = $this->attribute;
		$arrElem = unserialize($this->ownerModel->$nameElem);
		foreach($confArElemKeys[$nameElem] as $elem) {
			$valElem = $arrElem[$elem];
        	echo $elem.': --- '.CHtml::telField(get_class($this->model).'['.$nameElem.'_'.$elem.']',$valElem,$this->htmlOptions).'<br/>';
		}
	}
}
