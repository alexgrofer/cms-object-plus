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

class FormModel extends CActiveRecordBehavior {
	private $revelem = array();
    public function initform($POSTORGET, $params_f=array()) {
        $model = $this->getOwner();
        $confform = array('elements' => array());
        $dinamicForm = new EmptyForm();
        if(method_exists($model,'ElementsForm')) {
            $confform['elements'] = $model->ElementsForm();
            $rulesall = $model->rules();
        }
        else {
            if(!method_exists($model,'rules') || (method_exists($model,'rules') && !count($model->rules()))) {
                $rulesall = array(array(implode(',',array_keys($model->attributes)),'safe'));
            }
            
            $confform['elements'] = array_fill_keys(array_keys($model->attributes), array('type'=>'text'));
        }
        
        $oldelements = $confform['elements'];
        if(count($params_f)) {
            $arrnewelem = array();
            foreach($confform['elements'] as $key => $elemf) {
                if(in_array($key, $params_f)) {
                    $arrnewelem[$key] = $confform['elements'][$key];
                }
            }
            
            if(count($arrnewelem)) {
                $confform['elements'] = $arrnewelem;
            }
            
            foreach($params_f as $newparam) {
                if(is_array($newparam)) {
                    $confform['elements'][$newparam[1]] = $oldelements[$newparam[0]];
                    unset($confform['elements'][$newparam[0]]);
                    foreach($rulesall as $key => $rule) {
                        if(strpos($rule[0],$newparam[0])!==false) {
                            $this->revelem[$newparam[1]] = $newparam[0];
                            $rulesall[$key][0] = preg_replace('/(^|\,|\s)'.$newparam[0].'/','$1'.$newparam[1],$rule[0]);
                        }
                    }
                }
            }
        }
        $confform['buttons'] = array('send'=>array('type'=>'submit',));

        foreach($confform['elements'] as $key => $value) {
            $namemodelprop = $key;
            if(!property_exists($model, $key) && count($this->revelem)) {
                if(array_key_exists($key,$this->revelem)) {
                    $namemodelprop = $this->revelem[$key];
                }
                else {
                    continue;
                }
            }
            $dinamicForm->$key = $model->$namemodelprop;
            if(array_key_exists('EmptyForm',$POSTORGET)) {
                if(array_key_exists($key,$POSTORGET['EmptyForm'])) {
                    $dinamicForm->$key = $POSTORGET['EmptyForm'][$key];
                }
            }
        }
        foreach($model->attributes as $key => $value) {
            if(!array_key_exists($key,$confform['elements'])) {
                $dinamicForm->$key = $value;
            }
        }
        $dinamicForm->rules = $rulesall;
        $dinamicForm->attributeLabels = $model->attributeLabels();
        //error unicue
        foreach($dinamicForm->rules as $key => $arrrule) {
            if($arrrule[1]=='unique') {
                unset($dinamicForm->rules[$key]);
            }
        }
        $patch_files = array();
        $funk_files = array();
        //start prop
        if(method_exists(get_class($model),'get_properties')) {
        $arrconfcms = UCms::getInstance()->config;
        $currentproperties = $model->get_properties();
        foreach($model->uclass->properties as $prop) {
            $nameelem = $prop->codename.'prop_';
            $dinamicForm->$nameelem = '';
            if(array_key_exists('EmptyForm',$POSTORGET) && array_key_exists($nameelem, $POSTORGET['EmptyForm'])) {
                $dinamicForm->$nameelem = $POSTORGET['EmptyForm'][$nameelem];
            }
            else {
                $dinamicForm->$nameelem = $currentproperties[$prop->codename];
            }
            
            if($prop->minfield) $dinamicForm->rules[] = array($nameelem, 'length', 'min'=>$prop->minfield);
            if($prop->maxfield) $dinamicForm->rules[] = array($nameelem, 'length', 'max'=>$prop->maxfield);
            if($prop->required) $dinamicForm->rules[] = array($nameelem, 'required');
            if($prop->udefault) $dinamicForm->rules[] = array($nameelem, 'default', 'value'=>$prop->udefault);
            
            $nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];
            if($nametypef=='bool') $dinamicForm->rules[] = array($nameelem, 'boolean');
            if(array_key_exists($nametypef, $arrconfcms['rulesvalidatedef'])) {
                $addarrsett = array($nameelem);
                $parsecvs = str_getcsv($prop->setcsv,"\n");
                foreach($parsecvs as $keyval) {
                    if(trim($keyval)=='') continue;
                    if(strpos($keyval,'us_set')===false) {
                        if(strpos($keyval,'=>')===false) {
                            array_push($addarrsett,$keyval);
                        }
                        else {
                            list($typeval,$val) = explode('=>',trim($keyval));
                            $addarrsett[$typeval] = $val;
                        }
                    }
                    if(strpos($keyval,'us_set_patch')!==false) {
                        list($typeval,$val) = explode('=>',trim($keyval));
                        $patch_files[$nameelem] = $val;
                    }
                    if(strpos($keyval,'us_set_funk_lines_loader')!==false) {
                        list($typeval,$val) = explode('=>',trim($keyval));
                        $funk_files[$nameelem] = $val;
                    }
                }
                $dinamicForm->rules[] = $addarrsett;
            }
            
            $confform['elements'][$nameelem] = array('type' => $arrconfcms['TYPES_MYFIELDS'][$nametypef]);
        }
        }
        $form = new CForm($confform,$dinamicForm);
        if(array_key_exists('EmptyForm',$POSTORGET)!==false && $form->validate()) {
            if(!$model->id && method_exists(get_class($model),'get_properties')) $model->uclass_id = $model->uclass->id;
            foreach($POSTORGET['EmptyForm'] as $key => $value) {
                if($ObjCUploadedFile = CUploadedFile::getInstance($form->model,$key)) {
                    $path='';
                    $namefuncload='';
                    if(array_key_exists($key,$patch_files)) {
                        $path = $patch_files[$key];
                    }
                    if(array_key_exists($key,$funk_files)) {
                        $namefuncload = $funk_files[$key];
                    }
                    $value = $ObjCUploadedFile;
                    if(($posptop = strpos($key, 'prop_'))!==false) {
                        $value = array('ObjCUploadedFile'=>$ObjCUploadedFile,'path'=>$path,'funcload'=>$namefuncload);
                    }
                }
				
                //start prop
                if(($posptop = strpos($key, 'prop_'))!==false) {
                    $trynameprop = substr($key,0,$posptop);
                    $model->set_properties($trynameprop,$value);
                }
                //end prop
                else {
                    $namemodelprop = $key;
                    if(!property_exists($model, $key) && count($this->revelem)) {
                        $namemodelprop = $this->revelem[$key];
                    }
                    $model->$namemodelprop = $value;
                }
            }
        }
        return $form;
    }
}