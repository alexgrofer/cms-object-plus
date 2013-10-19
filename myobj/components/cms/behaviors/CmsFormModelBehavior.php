<?php
class EmptyForm extends CFormModel {
	public function __set($name, $value) {
		if(!property_exists(get_class($this), $name)) {
			$this->$name = $value;
		}
	}
	public $rules;
	public function rules()
	{
		return $this->rules;
	}
	public $attributeLabels;
	public function attributeLabels() {
		return $this->attributeLabels;
	}
}

class CmsFormModelBehavior extends CActiveRecordBehavior {
	private $revelem = array();
	public function initform($POSTORGET, $params_f=array(), $arbitrary_elements=array(), $addRules=array()) {
		$model = $this->getOwner();
		$confform = array('elements' => array());
		$dinamicForm = new EmptyForm();
		$rulesall = $model->rules();
		if(method_exists($model,'ElementsForm')) {
			$confform['elements'] = $model->ElementsForm();
		}
		else {
			if(!method_exists($model,'rules') || (method_exists($model,'rules') && !count($model->rules()))) {
				$rulesall = array(array(implode(',',array_keys($model->attributes)),'safe'));
			}

			$confform['elements'] = array_fill_keys(array_keys($model->attributes), array('type'=>'text'));
		}

		$confform['buttons'] = array('send'=>array('type'=>'submit',));

		foreach($confform['elements'] as $key => $value) {
			$namemodelprop = $key;
			$dinamicForm->$key = $model->$namemodelprop;
		}

		$dinamicForm->rules = $rulesall;
		if(count($addRules)) {
			$dinamicForm->rules = array_merge($dinamicForm->rules,$addRules);
		}
		$dinamicForm->attributeLabels = $model->attributeLabels();
		if($params_f) {
			$dinamicForm->attributeLabels = array_merge($dinamicForm->attributeLabels,$params_f);
		}

		//start prop

		//добавление произвольных элементов к форме
		//необходимо добавление анонимной функции для проверок - сделать
		//добавить возможность добавлять массив rule - сделать
		foreach($arbitrary_elements as $AElement) {
			$dinamicForm->$AElement['name'] = isset($AElement['def_value'])?$AElement['def_value']:'';
			if(isset($AElement['elem'])) {
				$confform['elements'][$AElement['name']] = $AElement['elem'];
			}
			else {
				throw new CException(Yii::t('cms','None elem html form "{nameelem}" ',
					array('{nameelem}'=>$AElement['name'])));
			}
			//$dinamicForm->rules[] = array($AElement['name'], 'safe');
			//array rules
			//set lamda function
		}
		$form = new CForm($confform,$dinamicForm);
		return $form;
	}
}