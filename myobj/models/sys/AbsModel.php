<?php
abstract class AbsModel extends CActiveRecord
{
	public function primaryKey()
	{
		return 'id';
	}
	public $isHeaderModel=false;
	public $old_attributes=array();
	public static function model($className=null)
	{
		if($className===null) {
			$class = new static();
			$className = get_class($class);
		}
		return parent::model($className);
	}

	private $_conditStart=array();
	public function setuiprop($array,$save_dbCriteria=null,$isclear=false) {
		// $array=array('condition'=>array(array('p1','<=','23', 'AND'), array('p2', true, 'IN(','1,2,3)', 'OR')),'select'=>array('*' | ['p1','p2','p3']),'order'=>array(array('p1','desc')[,array('p1')]?))
		if($save_dbCriteria===null) {
			$save_dbCriteria = $this->dbCriteria;
		}
		// олько новые условия - сетреть все предыдущие условия
		if($isclear && count($this->_conditStart)) {
			foreach(count($this->_conditStart) as $str_cond) {
				$save_dbCriteria->condition = str_replace($str_cond,'',$save_dbCriteria->condition);
			}
		}
		//для обычных моделей свойтва не трубуются
		if($this->isHeaderModel) $properties = $this->getallprop();
		$arrconfcms = Yii::app()->appcms->config;
		if(array_key_exists('condition',$array)) {
			$propYes = false;
			foreach($array['condition'] as $cond) {
				$typecond = (count($cond)<5)?'AND':$cond[4];
				//prop
				$isand = ((!count($this->_conditStart) && $save_dbCriteria->condition)?' AND ':'');
				if($cond[1]==true) {
					$propYes = true;
					if(!isset($properties[$cond[0]])) {
						throw new CException(Yii::t('cms','None prop "{prop}" object class  "{class}"',
							array('{prop}'=>$cond[0], '{class}'=>$this->uclass->codename)));
					}
					$critStr = $isand."(lines.".$arrconfcms['TYPES_COLUMNS'][$properties[$cond[0]]->myfield]." ".$cond[2]." ".$cond[3]." AND property_alias.codename='".$cond[0]."') ".$typecond." ";
				}
				//param
				else {
					$critStr = ((trim($save_dbCriteria->condition)=='' || (stripos($save_dbCriteria->condition,'and')!==false || stripos($save_dbCriteria->condition,'or')!==false))?'':' AND ').$cond[0].'  '.$cond[2].'  '.$cond[3].'  '.$typecond.' ';
				}
				$save_dbCriteria->condition .= $critStr;
				$this->_conditStart[] = $critStr;
			}
			if($propYes!='') {
				$save_dbCriteria->with['lines'] = array('with' => 'property_alias', 'together'=>true);
				$save_dbCriteria->with['uclass.properties'] = array();
			}
		}
		if(array_key_exists('order',$array) && count($array['order'])) {
			//если свойство в массив добавляется 3 элемент true
			$elem_order = $array['order'][0];
			$isprop = ((count($elem_order)==3 && $elem_order[2]==true)?true:false);
			$typf = (count($elem_order)>=2)?$elem_order[1]:'asc';

			if($isprop) { //is prop
				$save_dbCriteria->with['lines_alias'] = array();
				if(!isset($properties[$elem_order[0]])) {
					throw new CException(Yii::t('cms','None prop "{prop}" object class  "{class}"',
						array('{prop}'=>$elem_order[0], '{class}'=>$this->uclass->codename)));
				}
				$save_dbCriteria->with['lines_order'] = array('on'=>"lines_order.property_id=".$properties[$elem_order[0]]->id,'together'=>true);

				$typeprop = $arrconfcms['TYPES_COLUMNS'][$properties[$elem_order[0]]->myfield];
				$textsql = '(case when lines_order.'.$typeprop.' is null then 1 else 0 end) asc, lines_order.'.$typeprop.' '.$typf;
				$save_dbCriteria->order = $textsql;
				//необходимо при пагинации что бы не создавались одинаковые элементы
				$save_dbCriteria->addCondition('lines_order.id IS NOT NULL');
			}
			//is param
			else {
				$textsql = $elem_order[0].' '.$typf;
				$save_dbCriteria->order = $textsql;
			}
		}
		if(array_key_exists('limit',$array) && count($array['limit'])) {
			$save_dbCriteria->group = $this->tableAlias.'.id';
			$save_dbCriteria->limit = $array['limit']['limit'];
			$save_dbCriteria->offset = $array['limit']['offset'];;
		}
		$this->setDbCriteria($save_dbCriteria);
		return $this;
	}
	public function behaviors()
	{
		return array(
			'UserRelated'=>array(
				'class'=>'CmsRelatedBehavior',
			),
			'UserFormModel'=>array(
				'class'=>'CmsFormModelBehavior',
			),
		);
	}
	public function getMTMcol($model,$pkelem,$exp_select) {
		return $this->UserRelated->links_edit('select',$model,array($pkelem),$exp_select);
	}
	public function setMTMcol($model,$array_elems,$array_value) {
		return $this->UserRelated->links_edit('edit',$model,$array_elems,$array_value);
	}
	public function clearMTMLink($nameRelation, $isDELETE_CASCADE) { //использовать только для MTM, для HAS_MANY нет смысла так как поля не удаляются а апдейтятся
		if($isDELETE_CASCADE==false) {
			$this->UserRelated->links_edit('clear',$nameRelation);
		}
	}
	public $costRules=array();
	public function __set($name, $value) {
		if(!property_exists(get_class($this), $name)) {
			$this->$name = $value;
		}
	}
	protected $costElementsForm=array();
	public function rules() {
		if(method_exists(get_class($this),'get_properties')) {
			$arrconfcms = Yii::app()->appcms->config;
			$currentproperties = $this->get_properties();
			foreach($this->uclass->properties as $prop) {
				$nameelem = $prop->codename.'prop_';
				$this->$nameelem = '';
				if(array_key_exists('EmptyForm',$_POST) && array_key_exists($nameelem, $_POST['EmptyForm'])) {
					$this->$nameelem = $_POST['EmptyForm'][$nameelem];
				}
				else {
					$this->$nameelem = $currentproperties[$prop->codename];
				}

				if($prop->minfield) $this->costRules[] = array($nameelem, 'length', 'min'=>$prop->minfield);
				if($prop->maxfield) $this->costRules[] = array($nameelem, 'length', 'max'=>$prop->maxfield);
				if($prop->required) $this->costRules[] = array($nameelem, 'required');
				if($prop->udefault) $this->costRules[] = array($nameelem, 'default', 'value'=>$prop->udefault);

				$nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];
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
					}
					$this->costRules[] = $addarrsett;
				}
				//для остальных нужно прописать safe иначе не будут отображаться в редактировании объекта
				else {
					$this->costRules[] = array($nameelem, 'safe');
				}
				if($nametypef=='bool') $this->costRules[] = array($nameelem, 'boolean');
				if($nametypef=='url') $this->costRules[] = array($nameelem, 'url');
				if($nametypef=='email') $this->costRules[] = array($nameelem, 'email');

				$nametypef = $arrconfcms['TYPES_MYFIELDS_CHOICES'][$prop->myfield];

				$this->costElementsForm[$nameelem] = array('type' => $arrconfcms['TYPES_MYFIELDS'][$nametypef]);
			}
		}
		return $this->costRules;
	}
}
