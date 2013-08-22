<?php
class AdminController extends Controller {
	public $layout='/layouts/admin/column1';
	public $apcms;
	public $dicturls = array();
	public $param_contr = array(
		'current_class_name'=>null,'current_class_conf_array'=>null,
		'current_class_ass_name'=>null,'current_class_ass_conf_array'=>null,
	);
	public $paramsrender = array(
			'REND_thisparamsui'=>null,
			'REND_thispropsui'=>null,
			'REND_selectedarr'=>array(),
			'REND_order_by_def'=>array(),
			'REND_order_by'=>array(),
			'REND_editform'=>null,
			'REND_objclass'=>null,
			'REND_confmodel'=>null,
			'REND_acces_read'=>true,
			'REND_acces_write'=>true,
			'REND_relation'=>null,
			'REND_witch'=>null,
			'REND_find'=>null,
			'REND_selfobjrelationElements'=>null,
		);
	public function redirect($url,$terminate=true,$statusCode=302) {
		if(isset($this->actionParams['usercontroller'])) {
			$url = substr($url,strpos($url,'r=')+2);
			$url=$this->createUrl('/'.$url,array('usercontroller'=>'usernav'));
		}
		parent::redirect($url,$terminate,$statusCode);
	}
	public function setVarRender($name,$value) {
		$this->paramsrender[$name] = $value;
	}
	public function getUrlBeforeAction() {
		return substr(Yii::app()->request->url,0,strpos(Yii::app()->request->url,'action/'));
	}
	public function getMenuhtmlSub() {
		return $this->renderPartial('/sys/myui_sub');
	}
	public function run($actionID) {
		//login
		$noneadmin = true;
		if(!Yii::app()->user->isGuest) {
			$objectadmingroup = uClasses::getclass('groups_sys')->objects()->findByAttributes(array('vp2' => 'admincms'));
			if(in_array($objectadmingroup->vp1, Yii::app()->user->groupsident)) {
				$noneadmin = false;
			}
		}
		if($noneadmin) {
			$this->layout = '/layouts/admin/main';
			$this->render('/sys/user/autorize');
			Yii::app()->end();
		}
		//init urls
		$paramslisturl = explode('/',Yii::app()->request->getParam('r'));
		if(count($paramslisturl)<3) $this->redirect(Yii::app()->createUrl('myobj/admin/objects/models/classes'));
		$this->dicturls['admin'] = $this->createUrl($paramslisturl[1].'/');
		$this->dicturls['class'] = $paramslisturl[2];
		$this->dicturls['all'] = implode('/',array_slice($paramslisturl,1));
		$this->dicturls['paramslist'] = array_merge(array_slice($paramslisturl,3), array('','','','','','',''));
		$indexaction = array_search('action',$this->dicturls['paramslist']);
		$this->dicturls['action'] = (is_int($indexaction))?$this->dicturls['paramslist'][($indexaction+1)]:'';
		$this->dicturls['actionid'] = (is_int($indexaction))?$this->dicturls['paramslist'][($indexaction+2)]:'';

		//VARS
		$view = ($this->dicturls['action']=='edit')?'/sys/obj':'/sys/list';
		$modelAD = null;
		$params_extra_action_job = array();
		switch($this->dicturls['class']) { //switch url
			case 'objects':
				if($this->dicturls['paramslist'][0]=='class') {
					$actclass = \uClasses::getclass($this->dicturls['paramslist'][1]);
					$this->setVarRender('REND_objclass',$actclass);
						$this->param_contr['current_class_name'] = $actclass->codename;
						$this->param_contr['current_class_conf_array'] = Yii::app()->appcms->config['spacescl'][$actclass->tablespace];
					if(!(int)$this->dicturls['paramslist'][1]) {
						$this->redirect(Yii::app()->createUrl('myobj/admin/objects/class/'.$actclass->id));
					}
					$modelAD = $actclass->objects();
					//вытащить одним запросом вместе со свойствами
					$modelAD->set_force_prop(true);

					$settui = Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['conf_ui_classes'][$actclass->codename];
					$this->setVarRender('REND_confmodel',$settui);
				}
				elseif($this->dicturls['paramslist'][0]=='models' && $this->dicturls['paramslist'][1]!='') {
					//если не равно пост и есть relationobjonly=название модели
					//сделать для теста а потом уже тут полностью поправить
					$params_modelget = Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['models'][$this->dicturls['paramslist'][1]];
					//alias model
					if(is_string($params_modelget)) {
						$params_modelget = Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['models'][$params_modelget];

					}
					$this->setVarRender('REND_confmodel',$params_modelget);
					$NAMEMODEL_get = $params_modelget['namemodel'];

					$modelAD =  $NAMEMODEL_get::model();
					//permission show classes
					if(get_class($modelAD)=='uClasses') {
						$show_none_permission_classes = array();
						foreach(Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['conf_ui_classes'] as $key =>$class_conf) {
							if(
								isset($class_conf['groups_read']) &&
								$class_conf['groups_read'] &&
								!count(array_intersect(Yii::app()->user->groupsident,$class_conf['groups_read']))
							) {
								$show_none_permission_classes[] = $key;
							}
						}
						$modelAD->dbCriteria->addNotInCondition('codename', $show_none_permission_classes);
					}
					$settui = Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['models'][$this->dicturls['paramslist'][1]];

					//view links class obj
					if($this->dicturls['paramslist'][3]=='links') {
						$actclass = $modelAD->findByPk($this->dicturls['paramslist'][2]);
							$this->param_contr['current_class_name'] = $actclass->codename;
							$this->param_contr['current_class_conf_array'] = Yii::app()->appcms->config['spacescl'][$actclass->tablespace];
						$objctsassociation = $actclass->association;
						$modelAD->dbCriteria->addInCondition('id', apicms\utils\arrvaluesmodel($objctsassociation,'id'));
					}
				}
				elseif($this->dicturls['paramslist'][0]=='ui' && $this->dicturls['paramslist'][1]!='') {
					$settui = Yii::app()->appcms->config['controlui']['ui'][$this->dicturls['paramslist'][1]];
				}

				if(isset($settui)) {
				/// acces
				if(array_key_exists('groups_read',$settui) && $settui['groups_read']) {
					$result = array_intersect(Yii::app()->user->groupsident, $settui['groups_read']);
					if(count($result)==0) {
						$this->setVarRender('REND_acces_read',false);
					}
					unset($result);
				}
				if(array_key_exists('groups_write',$settui) && $settui['groups_write']) {
					$result = array_intersect(Yii::app()->user->groupsident, $settui['groups_write']);
					if(count($result)==0) {
						$this->setVarRender('REND_acces_write',false);
					}
					unset($result);
				}
				///// set setting
				if(array_key_exists('cols',$settui) && $settui['cols']) {
					$this->setVarRender('REND_thisparamsui',$settui['cols']);
				}
				elseif($modelAD) {
					$this->setVarRender('REND_thisparamsui',array_combine(array_keys($modelAD->attributes),array_keys($modelAD->attributes)));
				}
				if(array_key_exists('cols_props',$settui) && $settui['cols_props']) {
					$this->setVarRender('REND_thispropsui',$settui['cols_props']);
				}

				if(array_key_exists('order_by_def',$settui) && $settui['order_by_def']) {
					$this->setVarRender('REND_order_by_def',$settui['order_by_def']);
				}

				if(array_key_exists('order_by',$settui) && $settui['order_by']) {
					$this->setVarRender('REND_order_by',$settui['order_by']);
				}

				if(array_key_exists('edit', $settui) && $settui['edit'] && count($settui['edit'])) {
					$this->setVarRender('REND_editform',$settui['edit']);
				}

				if(array_key_exists('relation',$settui) && $settui['relation']) {
					$this->setVarRender('REND_relation',$settui['relation']);
				}

				$this->setVarRender('REND_find',(array_key_exists('find',$settui) && $settui['find'])?$settui['find']:$this->paramsrender['REND_thisparamsui']);

				if(array_key_exists('witch', $settui) && $settui['witch']) {
					$this->setVarRender('REND_witch',$settui['witch']);
					$modelAD->dbCriteria->with = $settui['witch'];
				}

				if(array_key_exists('selfobjrelationElements', $settui) && $settui['selfobjrelationElements']) {
					$this->setVarRender('REND_selfobjrelationElements',$settui['selfobjrelationElements']);
				}

				if(isset($settui['controller']) && is_array($settui['controller'])) {
					if(isset($settui['controller']['default']) && $settui['controller']['default']) {
						$namecontroller = $settui['controller']['default'];
					}
					elseif(isset($this->actionParams['usercontroller']) && $this->actionParams['usercontroller'] && isset($settui['controller'][$this->actionParams['usercontroller']])) {
						$namecontroller = $settui['controller'][$this->actionParams['usercontroller']];
					}
					if(isset($namecontroller)) require(dirname(__FILE__).'/cms/'.$namecontroller);
				}

				unset($settui);
				}
				/////
				// acces
				switch($this->dicturls['action']) {
					case 'edit':
					case 'edittempl':
						// acces
						if($this->paramsrender['REND_acces_write']==false && count($_POST)) $this->redirect($this->getUrlBeforeAction());
						//
						if((int)$this->dicturls['paramslist'][4]>0) {
							$modelAD = $modelAD->findByPk($this->dicturls['paramslist'][4]);
						}
						else {
							$modelAD = new $modelAD();
							if($this->dicturls['paramslist'][0]=='class') {
								$modelAD->uclass = $actclass;
							}
						}
						break;
					case 'lenksobjedit':
						$association_class = uClasses::getclass($this->dicturls['paramslist'][6]);
							$this->param_contr['current_class_ass_name'] = $association_class->codename;
							$this->param_contr['current_class_ass_conf_array'] = Yii::app()->appcms->config['spacescl'][$association_class->tablespace]['namemodel'];
						$getlinks = $association_class->objects()->findByPk($this->dicturls['paramslist'][4])->getobjlinks($this->dicturls['paramslist'][1]);
						if($getlinks) {
							$this->paramsrender['REND_selectedarr'] = apicms\utils\arrvaluesmodel($getlinks->findAll(),'id');
						}
						break;
					case 'relationobj':
					case 'relationobjonly':
						//найти в реляции название столбца по которому бдет поиск и установить ктитерию
						//объект пока не делаем
						$subnamemodel = $this->dicturls['paramslist'][7];
						$namemodelself = $this->dicturls['paramslist'][1];
						//selectedarr
						$params_modelget = Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['models'][$subnamemodel];
						//is alias
						if(!is_array($params_modelget)) {
							$namealias = $params_modelget;
							$params_modelget = Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['models'][$namealias];unset($namealias);
						}
						$NAMEMODEL_get = $params_modelget['namemodel'];
						$relation_model = $NAMEMODEL_get::model()->relations();
						$objrelated = $NAMEMODEL_get::model()->findByPk($this->dicturls['actionid']);
						//возможно что ссылка на медель в реляции может называться по другому но в следствии того что в списке настроек моделей уже есть настройка для нее
						if(isset($params_modelget['relation'][$namemodelself])) {
							$namemodelself = $params_modelget['relation'][$namemodelself];
						}
						$objrelself = $objrelated->$namemodelself;
						if($subnamemodel=='classes') {
							if($this->dicturls['paramslist'][1]=='classes') {
							//classes filter is NameLinksModel equally
							$ids_spaces_equal = array();
							foreach(Yii::app()->appcms->config['spacescl'] as $key => $value) {
								if($value['namelinksmodel']==Yii::app()->appcms->config['spacescl'][$objrelated->tablespace]['namelinksmodel']) $ids_spaces_equal[] = $key;
							}
							$modelAD->dbCriteria->addInCondition('tablespace', $ids_spaces_equal);
							}
							$this->param_contr['current_class_name'] = $objrelated->codename;
							$this->param_contr['current_class_conf_array'] = Yii::app()->appcms->config['spacescl'][$objrelated->tablespace];
						}

						if($this->dicturls['action'] == 'relationobjonly') {
							$type_relation_self = $relation_model[$namemodelself][0];
							if($type_relation_self  == CActiveRecord::MANY_MANY) {
								$modelAD->dbCriteria->addInCondition($modelAD->tableSchema->primaryKey, apicms\utils\arrvaluesmodel($objrelself,'id'));
							}
							else {
								if($type_relation_self  == CActiveRecord::BELONGS_TO) {
									if($objrelself) {
										$addCondition = $objrelself->tableAlias.'.'.$objrelself->primaryKey().' = '.$objrelself->getPrimaryKey();
									}
									else $addCondition = '1=0';
								}
								else {
									$addCondition = $relation_model[$namemodelself][2].' = '.$objrelated->primaryKey;
								}
								$modelAD->dbCriteria->addCondition($addCondition);
							}
						}
						if($this->paramsrender['REND_relation'] && array_key_exists($namemodelself,$this->paramsrender['REND_relation'])) {
							$namemodelself = $this->paramsrender['REND_relation'][$namemodelself];
						}
						$params_extra_action_job['name_model'] = $namemodelself;

						if($objrelself) {
							if(is_array($objrelself)) {
								$this->paramsrender['REND_selectedarr'] = apicms\utils\arrvaluesmodel($objrelself,'id');
							}
							else {
								$this->paramsrender['REND_selectedarr'] = array($objrelself->id);
							}
						}

						unset($namemodelself);
						break;
					case 'remove':
						if($this->paramsrender['REND_acces_write']==false) $this->redirect($this->getUrlBeforeAction());
						if((int)$this->dicturls['paramslist'][4]>0) {
							$modelAD = $modelAD->findByPk($this->dicturls['paramslist'][4]);
							$modelAD->delete();
							$this->redirect($this->getUrlBeforeAction());
						}
						break;
				}

				break;
			case 'logout':
				Yii::app()->user->logout();
				$this->redirect(Yii::app()->request->getUrlReferrer());
			default:
				$this->redirect(Yii::app()->createUrl('myobj/admin/objects/models/classes/'));
		}
		//saveaction
		if($this->paramsrender['REND_acces_write']!=false) {
			$selectorsids_post = (array_key_exists('selectorsids',$_POST) && trim($_POST['selectorsids'])!='')?explode(',',$_POST['selectorsids']):array();
			$selectorsids_excluded = (array_key_exists('selectorsids_excluded',$_POST) && trim($_POST['selectorsids_excluded'])!='')?explode(',',$_POST['selectorsids_excluded']):array();

			if(array_key_exists('saveaction',$_POST)) {
				apicms\utils\action_job($this->dicturls['action'],$this->dicturls['actionid'],$selectorsids_post, $selectorsids_excluded, $this->dicturls['paramslist'],$params_extra_action_job);

				$this->redirect(Yii::app()->request->url);
			}

			//utils delete, cvs
			if($this->dicturls['action']=='' && count($selectorsids_post)) {
				$linkAnFuncSetCritModel = function() use($modelAD,$selectorsids_post) {$modelAD->dbCriteria->addInCondition($modelAD->tableAlias.'.'.$modelAD->primaryKey(), $selectorsids_post);};

				if(array_key_exists('checkdelete',$_POST)) {
					$linkAnFuncSetCritModel();
					$objects = $modelAD->findAll();
					foreach($objects as $obj) {
						$obj->delete();
					}
					$this->redirect(Yii::app()->request->url);
				}
				elseif(array_key_exists('importcsv',$_POST)) {
					$linkAnFuncSetCritModel();
					Yii::import('ext.ECSVExport');
					$namefile = '';
					if($modelAD->isHeaderModel) {
						$modelAD->set_force_prop(true);
						$namefile = 'class-'.$actclass->codename;
					}
					else {
						$namefile = 'model-'.get_class($modelAD);
					}
					$objects = $modelAD->findAll();
					$most_array_all = array();
					foreach($objects as $obj) {
						$array_insert = $obj->attributes;
						$prop_array = array();
						if($modelAD->isHeaderModel) {
							$prop_array = $obj->get_properties();
							foreach($prop_array as $key => $val) {
								$prop_array[$key.'__prop'] = $val;
								unset($prop_array[$key]);
							}
						}
						$most_array_all[] = array_merge($array_insert,$prop_array);
					}
					$csv = new ECSVExport($most_array_all);

					$content = $csv->toCSV();
					$namefile .=  '_'.Yii::app()->dateFormatter->format('yyyy-MM-dd_HH-mm', time());//.'csv';
					$typefile = 'text/csv';
					$this->layout=false;
					$view = '/sys/sendfile';
					$paramsrender = array('namefile'=>$namefile,'content'=>$content,'typefile'=>$typefile,'terminate'=>false);
					$this->renderPartial($view,$paramsrender);
					Yii::app()->end();
				}
			}
			if(isset($_FILES['exportcsv']) && $_FILES['exportcsv']['tmp_name']) {
				$row = 1;
				$attributes_csv=array();
				$properties_csv=array();
				$headers_key_prop_csv=array();
				$headers_key_attr_csv=array();
				$count_data = 0;
				if (($handle = fopen($_FILES['exportcsv']['tmp_name'], 'r'))!==false) {
					$transaction=Yii::app()->db->beginTransaction();
					///transaction
					try {

					while (($data = fgetcsv($handle))!==false) {
						if($row==1) {
							$count_data = count($data);
							$headers_key_attr_csv = $data;
							foreach($data as $k => $nanecol) {
								if($posprop=strpos($nanecol,'__prop')) {
									$headers_key_prop_csv[$k] = substr($nanecol,0,$posprop);
								}
							}
						}
						elseif(count($data)==$count_data) {
							foreach($data as $k => $val) {
								if(isset($headers_key_prop_csv[$k])) {
									$properties_csv[$headers_key_prop_csv[$k]] = $val;
								}
								else {
									if($modelAD->primaryKey() && $headers_key_attr_csv[$k] == $modelAD->primaryKey() && !isset($_POST['exportcsv_ispk'])) continue;
									$attributes_csv[$headers_key_attr_csv[$k]] = $val;
								}
							}
							$namemodel= get_class($modelAD);
							$newobj = new $namemodel;
							$newobj->attributes = $attributes_csv;
							if(isset($attributes_csv[$modelAD->primaryKey()]) && isset($_POST['exportcsv_ispk'])) {
								$namepk = $modelAD->primaryKey();
								$newobj->$namepk = $attributes_csv[$modelAD->primaryKey()];
							}
							if($newobj->isHeaderModel) {
								$newobj->uclass_id = $attributes_csv['uclass_id'];
								$newobj->properties = $properties_csv;
							}
							$newobj->save();
							if($newobj->getErrors()) throw new CException(Yii::t('cms',serialize($newobj->getErrors())));
						}
						$row++;
					}

					///transaction
					}
					catch(Exception $e) {
						$transaction->rollBack();
						throw $e;
					}
					$transaction->commit();

					fclose($handle);
				}
				$this->redirect(Yii::app()->request->getUrlReferrer());
			}
		}
		if(isset($this->paramsrender['REND_acces_read']) && $this->paramsrender['REND_acces_read']===false) {
			$view = '/sys/acces';
		}
		$this->paramsrender['REND_model'] = $modelAD;
		$this->render($view, $this->paramsrender);
	}
}