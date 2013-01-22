<?php
Yii::import('application.modules.myobj.appscms.UCms');
Yii::import('application.modules.myobj.appscms.api.utils',true);

class AdminController extends Controller {
    public $layout='/layouts/admin/column1';
    public $apcms;
    public $dicturls = array();
    public $param_contr = array(
        'current_class_name'=>null,'current_class_spacename'=>null,
        'current_class_ass_name'=>null,'current_class_ass_spacename'=>null,
    );
    public $paramsrender = array(
            'REND_thisparamsui'=>null,
            'REND_thispropsui'=>null,
            'REND_selectedarr'=>array(),
            'REND_order_by_param'=>null,
            'REND_editform'=>null,
            'REND_objclass'=>null,
            'REND_confmodel'=>null,
            'REND_acces_read'=>true,
            'REND_acces_write'=>true,
            'REND_relation'=>null,
        );
    public function setVarRender($name,$value) {
        $this->paramsrender[$name] = $value;
    }
    public function getUrlBeforeAction() {
        return substr(Yii::app()->request->url,0,strpos(Yii::app()->request->url,'action/'));
    }
    public function run($actionID) {
        $this->apcms = UCms::getInstance($this);
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
            $this->render('/admin/user/autorize');
            Yii::app()->end();
        }
        //init urls
        $paramslisturl = explode('/',$this->actionParams['r']);
		if(count($paramslisturl)<3) $this->redirect(Yii::app()->createUrl('myobj/admin/objects/models/classes'));
        $this->dicturls['admin'] = $this->createUrl($paramslisturl[1].'/');
        $this->dicturls['class'] = $paramslisturl[2];
        $this->dicturls['all'] = implode('/',array_slice($paramslisturl,1));
        $this->dicturls['paramslist'] = array_merge(array_slice($paramslisturl,3), array('','','','','','',''));
        $indexaction = array_search('action',$this->dicturls['paramslist']);
        $this->dicturls['action'] = (is_int($indexaction))?$this->dicturls['paramslist'][($indexaction+1)]:'';
        $this->dicturls['actionid'] = (is_int($indexaction))?$this->dicturls['paramslist'][($indexaction+2)]:'';
        
        //VARS
        $view = ($this->dicturls['action']=='edit')?'/admin/obj':'/admin/list';
        $modelAD = null;
        $params_extra_action_job = array();
        switch($this->dicturls['class']) { //switch url
            case 'objects':
                if($this->dicturls['paramslist'][0]=='class') {
                    $actclass = \uClasses::getclass($this->dicturls['paramslist'][1]);
                    $this->setVarRender('REND_objclass',$actclass);
                        $this->param_contr['current_class_name'] = $actclass->codename;
                        $this->param_contr['current_class_spacename'] = $this->apcms->config['spacescl'][$actclass->tablespace]['namemodel'];
                    if(!(int)$this->dicturls['paramslist'][1]) {
                        $this->redirect(Yii::app()->createUrl('myobj/admin/objects/class/'.$actclass->id));
                    }
                    $modelAD = $actclass->objects();
                    
                    $settui = $this->apcms->config['controlui'][$this->dicturls['class']]['headers_spaces'][$this->apcms->config['spacescl'][$actclass->tablespace]['namemodel']];
                    
                    $findelem = 'default';
                    if(array_key_exists($actclass->codename,$settui)) {
                        $findelem = $actclass->codename;
                    }
                    $this->setVarRender('REND_confmodel',$settui[$findelem]);
                }
                elseif($this->dicturls['paramslist'][0]=='models' && $this->dicturls['paramslist'][1]!='') {
                    $params_modelget = $this->apcms->config['controlui'][$this->dicturls['class']]['models'][$this->dicturls['paramslist'][1]];
                    $findelem = $this->dicturls['paramslist'][1];
                    //alias model
                    if(is_string($params_modelget)) {
                        $findelem = $params_modelget;
                        $params_modelget = $this->apcms->config['controlui'][$this->dicturls['class']]['models'][$params_modelget];
                        
                    }
                    $this->setVarRender('REND_confmodel',$params_modelget);
                    $NAMEMODEL_get = $params_modelget['namemodel'];
                    
                    $modelAD =  $NAMEMODEL_get::model();
                    $settui = $this->apcms->config['controlui'][$this->dicturls['class']]['models'];
                    
                    //view links class obj
                    if($this->dicturls['paramslist'][3]=='links') {
                        $actclass = $modelAD->findByPk($this->dicturls['paramslist'][2]);
                            $this->param_contr['current_class_name'] = $actclass->codename;
                            $this->param_contr['current_class_spacename'] = $this->apcms->config['spacescl'][$actclass->tablespace]['namemodel'];
                        $objctsassociation = $actclass->association;
                        $modelAD->dbCriteria->addInCondition('id', apicms\utils\arrvaluesmodel($objctsassociation,'id'));
                    }
                }
                elseif($this->dicturls['paramslist'][0]=='ui' && $this->dicturls['paramslist'][1]!='') {
                    $settui = $this->apcms->config['controlui']['ui'];
                    $findelem = $this->dicturls['paramslist'][1];
                }
                
                if(isset($settui)) {
                /// acces
                if(array_key_exists('groups_read',$settui[$findelem]) && $settui[$findelem]['groups_read']) {
                    $result = array_intersect(Yii::app()->user->groupsident, $settui[$findelem]['groups_read']);
                    if(count($result)==0) {
                        $this->setVarRender('REND_acces_read',false);
                    }
                    unset($result);
                }
                if(array_key_exists('groups_write',$settui[$findelem]) && $settui[$findelem]['groups_write']) {
                    $result = array_intersect(Yii::app()->user->groupsident, $settui[$findelem]['groups_write']);
                    if(count($result)==0) {
                        $this->setVarRender('REND_acces_write',false);
                    }
                    unset($result);
                }
                ///// set setting
                if(array_key_exists('cols',$settui[$findelem])) {
                    $this->setVarRender('REND_thisparamsui',$settui[$findelem]['cols']);
                }
                if(array_key_exists('cols_props',$settui[$findelem])) {
                    $this->setVarRender('REND_thispropsui',$settui[$findelem]['cols_props']);
                }
                
                if(array_key_exists('order_by',$settui[$findelem])) {
                    $this->setVarRender('REND_order_by_param',$settui[$findelem]['order_by']);
                }
                
                if(array_key_exists('edit', $settui[$findelem]) && count($settui[$findelem]['edit'])) {
                    $this->setVarRender('REND_editform',$settui[$findelem]['edit']);
                }
                
                if(array_key_exists('relation',$settui[$findelem])) {
                    $this->setVarRender('REND_relation',$settui[$findelem]['relation']);
                }
                
                $namecontroller = (array_key_exists('controller', $settui[$findelem]))?$settui[$findelem]['controller']:null;
                if($namecontroller!==null) {
                    require(dirname(__FILE__).'/cms/'.$namecontroller);
                }
                unset($findelem,$settui,$namecontroller);
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
                            $this->param_contr['current_class_ass_spacename'] = $this->apcms->config['spacescl'][$association_class->tablespace]['namemodel'];
                        $getlinks = $association_class->objects()->findByPk($this->dicturls['paramslist'][4])->getobjlinks($this->dicturls['paramslist'][1]);
                        if($getlinks) {
                            $this->paramsrender['REND_selectedarr'] = apicms\utils\arrvaluesmodel($getlinks->findAll(),'id');
                        }
                        break;
                    case 'relationobj':
                        //selectedarr
                        $params_modelget = $this->apcms->config['controlui'][$this->dicturls['class']]['models'][$this->dicturls['paramslist'][7]];
                        //is alias
                        if(!is_array($params_modelget)) {
                            $namealias = $params_modelget;
                            $params_modelget = $this->apcms->config['controlui'][$this->dicturls['class']]['models'][$namealias];unset($namealias);
                        }
                        $NAMEMODEL_get = $params_modelget['namemodel'];
                        $objrelated = $NAMEMODEL_get::model()->findByPk($this->dicturls['actionid']);
                        if($this->dicturls['paramslist'][7]=='classes') {
                            if($this->dicturls['paramslist'][1]=='classes') {
                            //classes filter is NameLinksModel equally
                            $ids_spaces_equal = array();
                            foreach($this->apcms->config['spacescl'] as $key => $value) {
                                if($value['namelinksmodel']==$this->apcms->config['spacescl'][$objrelated->tablespace]['namelinksmodel']) $ids_spaces_equal[] = $key;
                            }
                            $modelAD->dbCriteria->addInCondition('tablespace', $ids_spaces_equal);
                            }
                            $this->param_contr['current_class_name'] = $objrelated->codename;
                            $this->param_contr['current_class_spacename'] = $this->apcms->config['spacescl'][$objrelated->tablespace]['namemodel'];
                        }
                        $namemodelself = $this->dicturls['paramslist'][1];
                        if($this->paramsrender['REND_relation'] && array_key_exists($namemodelself,$this->paramsrender['REND_relation'])) {
                            $namemodelself = $this->paramsrender['REND_relation'][$namemodelself];
                        }
                        $params_extra_action_job['name_model'] = $namemodelself;
                        $objrelself = $objrelated->$namemodelself;
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
                if(array_key_exists('checkdelete',$_POST)) {
                    $modelAD->dbCriteria->addInCondition($modelAD->tableAlias.'.id', $selectorsids_post);
                    $objects = $modelAD->findAll();
                    foreach($objects as $obj) {
                        $obj->delete();
                    }
                    $this->redirect(Yii::app()->request->url);
                }
            }
        }
        //list UI or Models
        if($this->dicturls['paramslist'][1]=='' && in_array($this->dicturls['paramslist'][0],array('models','ui'))) {
            $view = '/admin/listmodelui';
        }
        if($this->paramsrender['REND_acces_read']===false) {
            $view = '/admin/acces';
        }
        $this->paramsrender['REND_model'] = $modelAD;
        $this->render($view, $this->paramsrender);
    }
}
