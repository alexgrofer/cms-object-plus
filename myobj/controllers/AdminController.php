<?php
namespace MYOBJ\controllers;
use \yii as yii;
use \CActiveRecord as CActiveRecord;

class AdminController extends \Controller {
	public $layout='/admin/layouts/column1';
	public $apcms;
	public $dicturls = array();

	public $paramsrender = array(
			'REND_thisparamsui'=>null,
			'REND_thispropsui'=>null,
			'REND_selectedarr'=>array(),
			'REND_order_by_def'=>array(),
			'REND_order_by'=>array(),
			'REND_AttributeLabels'=>null,
			'REND_editForm'=>null,
			'REND_acces_read'=>true,
			'REND_acces_write'=>true,
			'REND_relation'=>null,
			'REND_witch'=>null,
			'REND_find'=>null,
			'REND_selfobjrelationElements'=>null,
		);

	public function setVarRender($name,$value) {
		$this->paramsrender[$name] = $value;
	}

	/**
	 * Некоторые url нужно средиректить в исходное состояние до action/
	 * @return string
	 * @throws \CException
	 */
	public function getUrlBeforeAction() {
		if(($pos = strpos(Yii::app()->request->url,'action/'))===false) {
			return Yii::app()->request->url;
			//throw new \CException(Yii::t('cms','url not corresponds pattern action/'));
		}
		return substr(Yii::app()->request->url,0,strpos(Yii::app()->request->url,'action/'));
	}
	public function getMenuhtmlSub() {
		return $this->renderPartial('/admin/views/myui_sub');
	}

	/**
	 * Так как на данный момент я НЕ работаю через нормальную схему controller/action пишу в рут всю строку
	 * что бы узнать полный путь без параметро GET Yii::app()->createUrl($this->route);
	 * @return string
	 */
	public function getRoute() {
		return Yii::app()->request->getParam('r');
	}

	/**
	 * Так как в основном все реализованно на одном экшене admin, проще получать ссылку через метод createAdminUrl
	 * @param $route
	 * @param array $params
	 * @param string $ampersand
	 * @return string
	 */
	public function createAdminUrl($route,$params=array(),$ampersand='&') {
		return parent::createUrl('admin/'.$route, $params,$ampersand);
	}

	public function run($actionID) {
		//login
        if(defined('YII_DEBUG') && YII_DEBUG){
            //Yii::app()->assetManager->forceCopy = true;
        }
		if(Yii::app()->user->isGuest) {
			$this->render('/admin/views/user/autorize');
			Yii::app()->end();
		}
		//init urls
		$paramslisturl = explode('/',Yii::app()->request->getParam('r'));
		if(count($paramslisturl)<3) $this->redirect(Yii::app()->createUrl('myobj/admin/objects/models/classes'));
		$this->dicturls['class'] = $paramslisturl[2];
		$this->dicturls['paramslist'] = array_merge(array_slice($paramslisturl,3), array('','','','','','',''));
		$indexaction = array_search('action',$this->dicturls['paramslist']);
		$this->dicturls['action'] = (is_int($indexaction))?$this->dicturls['paramslist'][($indexaction+1)]:'';
		$this->dicturls['actionid'] = (is_int($indexaction))?$this->dicturls['paramslist'][($indexaction+2)]:'';

		$objectsDelete = array();

		//VARS
		$view = ($this->dicturls['action']=='edit')?'/admin/views/obj':'/admin/views/list';
		$modelAD = null;
		switch($this->dicturls['class']) { //switch url
			case 'objects':
				$params_modelget = \SysUtils::normalAliasModel($this->dicturls['paramslist'][1]);
				$settui = $params_modelget;

				if($this->dicturls['paramslist'][0]=='class') {
					$actclass = \uClasses::getclass($this->dicturls['paramslist'][1]);
					$modelAD = $actclass->objects();
				}
				elseif($this->dicturls['paramslist'][0]=='models' && $this->dicturls['paramslist'][1]!='') {
					//alias model
					$NAMEMODEL_get = $params_modelget['namemodel'];
					$modelAD =  new $NAMEMODEL_get();
					//permission show classes
					if(get_class($modelAD)=='uClasses') {
						$show_none_permission_classes = array();
						foreach(Yii::app()->appcms->config['controlui'][$this->dicturls['class']]['conf_ui_classes'] as $key =>$class_conf) {
							//task group list view class
						}
						$modelAD->dbCriteria->addNotInCondition('codename', $show_none_permission_classes);
					}

					//view links class obj
					if($this->dicturls['paramslist'][3]=='links') {
						$actclass = \uClasses::getclass($this->dicturls['paramslist'][2]);
						$name_relat_association = Yii::app()->request->getParam('is_bask_link')==false?'association':'association_back';
						$objctsassociation = $actclass->$name_relat_association;
						$modelAD->dbCriteria->addInCondition('id', \SysUtils::arrvaluesmodel($objctsassociation,'id'));
					}
				}
				elseif($this->dicturls['paramslist'][0]=='ui' && $this->dicturls['paramslist'][1]!='') {
					$settui = Yii::app()->appcms->config['controlui']['ui'][$this->dicturls['paramslist'][1]];
				}
				//conf
				if(array_key_exists('group_read',$settui) && $settui['group_read']) {
					$this->setVarRender('REND_acces_read',Yii::app()->user->checkAccess($settui['group_read']));
				}
				if(array_key_exists('group_write',$settui) && $settui['group_write']) {
					$this->setVarRender('REND_acces_write',Yii::app()->user->checkAccess($settui['group_write']));
				}
				$this->setVarRender('REND_thisparamsui',isset($settui['cols']) ?
					$settui['cols']:
					array_combine(array_keys($modelAD->attributes),array_keys($modelAD->attributes))
				);
				if(array_key_exists('cols_props',$settui) && $settui['cols_props']) {
					$this->setVarRender('REND_thispropsui',$settui['cols_props']);
				}
				if(array_key_exists('order_by_def',$settui) && $settui['order_by_def']) {
					$this->setVarRender('REND_order_by_def',$settui['order_by_def']);
				}
				if(array_key_exists('order_by',$settui) && $settui['order_by']) {
					$this->setVarRender('REND_order_by',$settui['order_by']);
				}
				if(array_key_exists('AttributeLabels', $settui) && $settui['AttributeLabels'] && count($settui['AttributeLabels'])) {
					$this->setVarRender('REND_AttributeLabels',$settui['AttributeLabels']);
				}
				if(isset($settui['editForm']) && count($settui['editForm'])) {
					$this->setVarRender('REND_editForm',$settui['editForm']);
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
				//end config

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
							//объявляем новый объект
							$modelAD->declareObj();
						}
						break;
					case 'lenksobjedit':
						$association_class = \uClasses::getclass($this->dicturls['paramslist'][6]);
						$getlinks = $association_class->objects()->findByPk($this->dicturls['paramslist'][4])->getobjlinks($this->dicturls['paramslist'][1], Yii::app()->request->getParam('type_link'), Yii::app()->request->getParam('is_bask_link',false));
						$this->paramsrender['REND_selectedarr'] = \SysUtils::arrvaluesmodel($getlinks->findAll(),'id');
						break;
					case 'relationobj':
					case 'relationobjonly':
						//найти в реляции название столбца по которому бдет поиск и установить ктитерию
						//объект пока не делаем
						$subnamemodel = $this->dicturls['paramslist'][7];
						$namemodelthis = $this->dicturls['paramslist'][1];
						//selectedarr
						$params_modelget = \SysUtils::normalAliasModel($namemodelthis);

						$NAMEMODEL_get = $params_modelget['namemodel'];
						$modelAD_LINK =  new $NAMEMODEL_get();
						$relation_model = $modelAD_LINK->relations();

						$nameConfModelSelf = $this->dicturls['paramslist'][7];

						$namemodelrelationtop = $params_modelget['relation'][$nameConfModelSelf][0];
						$params_modelgettop = \SysUtils::normalAliasModel($namemodelrelationtop);
						$objrelated = $params_modelgettop['namemodel']::model()->findByPk($this->dicturls['actionid']);

						$nameRelatModelSelf = $params_modelget['relation'][$nameConfModelSelf][1];
						$objectsRelTop = $objrelated->$nameRelatModelSelf;

						$objrelself = $objectsRelTop;
						if($subnamemodel=='classes') {
							if($this->dicturls['paramslist'][1]=='classes') {
							//classes filter is NameLinksModel equally
							$ids_spaces_equal = array();
							foreach(Yii::app()->appcms->config['spacescl'] as $key => $value) {
								if($value['namelinksmodel']==$objrelated->getNameLinksModel()) $ids_spaces_equal[] = $key;
							}
							$modelAD->dbCriteria->addInCondition('tablespace', $ids_spaces_equal);
							}
						}

						if($this->dicturls['action'] == 'relationobjonly') {
							$type_relation_self = $relation_model[$nameConfModelSelf][0];
							if(!$objrelself) {
								$addCondition = '1=0'; //просто не показываем ни одного объекта так как не ожной связки
								$modelAD->dbCriteria->addCondition($addCondition);
							}
							else {
								if($type_relation_self  == CActiveRecord::MANY_MANY) {
									$modelAD->dbCriteria->addInCondition($modelAD->tableSchema->primaryKey, \SysUtils::arrvaluesmodel($objrelself,$modelAD->tableSchema->primaryKey));
								}
								elseif($type_relation_self==CActiveRecord::HAS_ONE || $type_relation_self==CActiveRecord::HAS_MANY) {
									//тут переделать причем равняять на USER in_array($typeThisRelation, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY))
									//показываем
									$modelAD->dbCriteria->addInCondition($modelAD->tableAlias.'.'.$modelAD->tableSchema->primaryKey, \SysUtils::arrvaluesmodel($objrelself,$modelAD->tableSchema->primaryKey));
								}
								elseif($type_relation_self  == CActiveRecord::BELONGS_TO) {
									$modelAD->dbCriteria->addInCondition($modelAD->tableAlias.'.'.$relation_model[$nameConfModelSelf][2], \SysUtils::arrvaluesmodel($objrelself,$relation_model[$nameConfModelSelf][2]));
								}
							}
						}

						if($objrelself) {
							if(is_array($objrelself)) {
								$this->paramsrender['REND_selectedarr'] = \SysUtils::arrvaluesmodel($objrelself,'id');
							}
							else {
								$this->paramsrender['REND_selectedarr'] = array($objrelself->primaryKey);
							}
						}
						break;
					case 'remove':
						if($this->paramsrender['REND_acces_write']==false) $this->redirect($this->getUrlBeforeAction());
						if((int)$this->dicturls['paramslist'][4]>0) {
							$modelAD = $modelAD->findByPk($this->dicturls['paramslist'][4]);
							$objectsDelete[] = $modelAD;
						}
						break;
				}

				break;
			case 'logout':
				Yii::app()->user->logout();
				$this->redirect(Yii::app()->request->url);
			default:
				$this->redirect(Yii::app()->createUrl('myobj/admin/objects/models/classes/'));
		}
		//saveaction
		if($this->paramsrender['REND_acces_write']!=false) {
			$selectorsids_post = (array_key_exists('selectorsids',$_POST) && trim($_POST['selectorsids'])!='')?explode(',',$_POST['selectorsids']):array();
			$selectorsids_excluded = (array_key_exists('selectorsids_excluded',$_POST) && trim($_POST['selectorsids_excluded'])!='')?explode(',',$_POST['selectorsids_excluded']):array();

			if(array_key_exists('saveaction',$_POST)) {
				$this->actionLinkHelper($selectorsids_post, $selectorsids_excluded);

				$this->redirect(Yii::app()->request->url);
			}

			//utils delete, cvs
			if($this->dicturls['action']=='' && count($selectorsids_post)) {
				$linkAnFuncSetCritModel = function() use($modelAD,$selectorsids_post) {$modelAD->dbCriteria->addInCondition($modelAD->tableAlias.'.'.$modelAD->primaryKey(), $selectorsids_post);};

				if(array_key_exists('checkdelete',$_POST)) {
					$linkAnFuncSetCritModel();
					$objects = $modelAD->findAll();
					foreach($objects as $obj) {
						$objectsDelete[] = $obj;
					}
				}
				elseif(array_key_exists('importcsv',$_POST)) {
					$linkAnFuncSetCritModel();
					Yii::import('ext.ECSVExport');
					$namefile = '';
					if($modelAD instanceof \AbsBaseHeaders) {
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
						if($modelAD instanceof \AbsBaseHeaders) {
							$prop_array = $obj->uProperties;
							foreach($prop_array as $key => $val) {
								$prop_array[$key.'__prop'] = $val;
								unset($prop_array[$key]);
							}
						}
						$most_array_all[] = array_merge($array_insert,$prop_array);
					}
					$csv = new \ECSVExport($most_array_all);

					$content = $csv->toCSV();
					$namefile .=  '_'.Yii::app()->dateFormatter->format('yyyy-MM-dd_HH-mm', time()).'.csv';
					$typefile = 'text/csv';
					$this->layout=false;
					$view = '/admin/views/sendfile';
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
							$newobj = new $namemodel();

							if($newobj instanceof \AbsBaseHeaders) {
								$newobj->uclass_id = $attributes_csv['uclass_id'];
								if(count($properties_csv)) {
									foreach($properties_csv as $keyP => $keyP) {
										$newobj->uProperties = [$keyP,$keyP];
									}
								}
							}

							$newobj->declareObj();
							$newobj->attributes = $attributes_csv;
							if(isset($attributes_csv[$modelAD->primaryKey()]) && isset($_POST['exportcsv_ispk'])) {
								$namepk = $modelAD->primaryKey();
								$newobj->$namepk = $attributes_csv[$modelAD->primaryKey()];
							}

							$newobj->save();
							if($newobj->getErrors()) throw new \CException(Yii::t('cms',serialize($newobj->getErrors())));
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
				$this->redirect(Yii::app()->request->url);
			}
		}

		$this->paramsrender['REND_model'] = $modelAD;
		//USER CONTROLLER
		if(isset($settui['controller']) && is_array($settui['controller'])) {
			if(isset($settui['controller']['default']) && $settui['controller']['default']) {
				$namecontroller = $settui['controller']['default'];
			}
			elseif(isset($this->actionParams['usercontroller']) && $this->actionParams['usercontroller'] && isset($settui['controller'][$this->actionParams['usercontroller']])) {
				$namecontroller = $settui['controller'][$this->actionParams['usercontroller']];
			}
			if(isset($namecontroller)) require(dirname(__FILE__).'/admin/sys/'.$namecontroller);
		}

		//delete objects
		if($objectsDelete) {
			foreach($objectsDelete as $obj) {
				$obj->delete();
			}
			$this->redirect($this->getUrlBeforeAction());
		}

		$this->render($view, $this->paramsrender);
	}

	function actionLinkHelper($listset=array(),$listsetexcluded=array()) {
		switch($this->dicturls['action']) {
			case 'lenksobjedit':
				$ObjHeader = \uClasses::getclass($this->dicturls['paramslist'][6])->objects()->findByPk($this->dicturls['actionid']);
				if(count($listset)) {
					$ObjHeader->editlinks('add',$this->dicturls['paramslist'][1],$listset,Yii::app()->request->getParam('type_link'),Yii::app()->request->getParam('is_bask_link',false));
				}
				if(count($listsetexcluded)) {
					$ObjHeader->editlinks('remove',$this->dicturls['paramslist'][1],$listsetexcluded,Yii::app()->request->getParam('type_link'),Yii::app()->request->getParam('is_bask_link',false));
				}
				break;
			case 'relationobj':
			case 'relationobjonly';
				$params_modelget = \SysUtils::normalAliasModel($this->dicturls['paramslist'][1]);

				$NAMEMODEL_get = \SysUtils::normalAliasModel($params_modelget['relation'][$this->dicturls['paramslist'][7]][0]);

				$obj = $NAMEMODEL_get['namemodel']::model()->findByPk($this->dicturls['actionid']);
				$nameRelation = $params_modelget['relation'][$this->dicturls['paramslist'][7]][1];

				if(count($listsetexcluded)) {
					$objRelations = $obj->relations();
					$addparam = array();
					//если ключ внейний и юзер пытается установить ключу null(отвязывает элемент) нежно заапдейтить новый
					if(count($listset) && $objRelations[$nameRelation][0]==\CActiveRecord::BELONGS_TO) {
						$addparam = array($listset[0]);
					}
					$obj->UserRelated->links_edit('remove',$nameRelation,$listsetexcluded,$addparam);
				}

				if(count($listset)) {
					$obj->UserRelated->links_edit('add',$nameRelation,$listset);
				}
		}
	}
}