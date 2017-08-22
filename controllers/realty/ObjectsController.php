<?php
use MYOBJ\appscms\core\business\realty\CategoryEnumeration;

class ObjectsController extends AbsSite {
	const MAX_USER_OBJECTS_SAVE_LIST_MAIL=10;
	const MAX_GEO_LIMIT = 3000;

	const COL_PRICE = 'price'; //если будет работать медленно заменить это название на price_one и раскомментаровать goto345234, но нужно будет запросом все поменять все старые записи по типу который описан goto345234

	protected function getActionsBlockIpCaptcha() {
		return array(
			'show'=>array('show', 3),
			'edit'=>array('edit_error_user', 2),
			'edit_preshow'=>array('edit_error_user', 2),
			'edit_success'=>array('edit_success', 2),
			'set_top'=>array('set_top', 2),
		);
	}
	protected function getActionsBlockIpRefrash() {
		return array(
			'list'=>2,
		);
	}

	public function filters() {
		return array(
			'accessControl'
		);
	}

	public function accessRules() {
		return array(

			array('deny', //запретить гостям
				'actions'=>array(
					'edit',
					'add',
					'edit_obj_map',
					'save_list_edit',
					'my',
				),
				'users'=>array('?'),
			),

			array('allow', //разрешить только модераторам
				'actions'=>array(
					'moderation_list',
					'moderation_images',
					'moderation_edit',
				),
				'roles'=>array('moderator'),
			),
			array('deny', //остальным запретить (ставится последним так как если нашел правило то след фильтр не срабатывает)
				'actions'=>array(
					'moderation_list',
					'moderation_images',
					'moderation_edit',
				),
			),

		);
	}

	public function actionSave_list_edit() {
		$is_saved = false;

		$form = \MYOBJ\appscms\core\base\form\DForm::create();
		$form->addAttributeRule('elem_name', 'required');
		$form->addAttributeLabel('elem_name', Yii::t('rl', 'Title'));
		$form->addAttributeRule('elem_period', 'required');
		$form->addAttributeLabel('elem_period', Yii::t('rl', 'Period'));
		//hidden
		$form->addAttributeRule('edit_id', 'safe');
		$form->addAttributeRule('new_elem', 'safe');
		$form->addAttributeRule('del_id', 'safe');
		$form->addAttributeRule('no_save_req', 'safe');
		$form->addAttributeRule('country', 'safe');
		$form->addAttributeRule('params_all', 'safe', Yii::app()->request->getParam('params_all', ''));
		$form->addAttributeRule('type_f_save_list', 'safe', Yii::app()->request->getParam('type_f_save_list', ''));
		$params_all = $form->params_all;
		$type_f_save_list = $form->type_f_save_list;

		$countListMail = UserSaveAdvertList::model()->count(
			'user_id='.Yii::app()->user->id.' AND period_send_email<>'.UserSaveAdvertList::TYPE_EMAIL_SEND_T0
		);

		if($getPost = Yii::app()->request->getPost(NAME_DF)) {
			$form->setAttributes($getPost);
			$params_all = $form->params_all;
			$type_f_save_list = $form->type_f_save_list;

			if($form->del_id) {
				UserSaveAdvertList::model()->deleteByPk($form->del_id);
				$form->unsetAttributes();
			}
			else {
				if ($form->edit_id) {
					$object = UserSaveAdvertList::model()->findByPk($form->edit_id);
				}

				if($form->no_save_req) {
					$form->elem_name = $object->name;
					$form->elem_period = $object->period_send_email;
				}
				elseif($form->validate()) {
					if (!$form->edit_id) {
						$object = new UserSaveAdvertList();
					}
					$object->user_id = Yii::app()->user->id;
					$object->name = $form->elem_name;
					$object->period_send_email = $form->elem_period;
					if($object->isNewRecord) { //сохранять только у новых
						$object->params = $params_all;
					}

					if($countListMail+1>static::MAX_USER_OBJECTS_SAVE_LIST_MAIL && $form->elem_period) {
						$form->addError('name', Yii::t('rl', 'вы не можете сохранить больше '.static::MAX_USER_OBJECTS_SAVE_LIST_MAIL.' элементов c ПОЧТОВОЙ рассылкой'));
					}
					else {
						$formCheckParams = \MYOBJ\appscms\core\base\form\DForm::create();
						$rulesTypeParams = Advert::rulesCatalogTypesFilter($type_f_save_list, $form->country);
						$formCheckParams->addRulesAR($rulesTypeParams);
						$formCheckParams->setAttributes(CJSON::decode($params_all));
						$object->save();
						$is_saved = true;
						$form->edit_id = $object->id;
					}
				}
			}
		}

		$elements = UserSaveAdvertList::model()->findAllByAttributes(array(
			'user_id'=>Yii::app()->user->id,
		), array('order'=>'t.id DESC'));

		if(!$form->edit_id) {
			$params_all_decode = CJSON::decode($params_all);
			$f_r = function($ar,$ar2) {
				return \MYOBJ\appscms\core\base\SysUtilsArray::array_diff_assoc_recursive($ar,$ar2);
			};
			foreach ($elements as $elemSave) {
				$elemSaveParams = CJSON::decode($elemSave->params);
				if(!$f_r($params_all_decode, $elemSaveParams) and !$f_r($elemSaveParams, $params_all_decode)) {
					$form->edit_id = $elemSave->id;
					$form->elem_name = $elemSave->name;
					$form->elem_period = $elemSave->period_send_email;
					break;
				}
			}
		}

		$form->params_all = $params_all;
		$form->type_f_save_list = $type_f_save_list;

		if (Yii::app()->request->isAjaxRequest) {
			$this->layout = null;
		}

		$this->render(DIR_VIEWS_SITE.'realty/objects/save_list_edit', array(
			'var_form'=>$form,
			'var_elements' => $elements,
			'var_is_saved' => $is_saved,
		));
	}

	public function actionEdit_obj_map($type, $token) {
		if($type=='switch') {
			$this->setSessionToken($token, 'result_geocoder_result', $_POST['result']);

			yii::app()->end('1');
		}

		yii::app()->end('0');
	}

	public function actionModeration_edit($id) {
		$modelAdvert = Advert::model()->findByPk($id);

		$moder_new_params = $modelAdvert->getEArray('moder_new_params');
		$params = array_merge($modelAdvert->getAttributes(), $moder_new_params);

		$is_force = 1; //Yii::app()->request->getParam('is_force')

		$images = array();
		if($images_moderate = $modelAdvert->images_moderate) {
			$images = yii::app()->StoreFile->getUrl(array_keys($images_moderate), StoreFile::TYPE_IMAGE,
				array('add_file' => ImageStore::TYPE_IMAGE_SMALL));
		}

		$true = Yii::app()->request->getParam('true');
		if($_POST && ($modelAdvert->is_moder!=0 || $is_force)) {
			//images
			$trueImage = Yii::app()->request->getParam('trueImageOriginal', array());
			//вначале сохраняем то что прошли модерацию
			if($trueImage) {
				$this->eachSaveImagesAccess($trueImage);
			}
			//дальше сохраняем то что не прошли
			$falseComment = Yii::app()->request->getParam('falseComment');
			$diffFalse = array_diff(array_keys($images), $trueImage);
			$this->eachSaveImagesAccess($diffFalse, false, $falseComment);
			//

			$params_is_edit = false;
			$oldStatus = $modelAdvert->status;
			if($true==1) {
				$modelAdvert->status = Advert::STATUS_PUBLIC;
				$modelAdvert->setScenario('catalog_type_' . $modelAdvert->type);
				$modelAdvert->moder_new_params = '';

				//params
				if ($is_edit_params = Yii::app()->request->getParam('is_edit_params')) {
					$edit_params = Yii::app()->request->getParam('edit_params');
					foreach ($edit_params as $kp => $val_p) {
						if(isset($is_edit_params[$kp])) {
							$params_is_edit = true;
							$modelAdvert->$kp = $val_p;
						}
					}
				}
			}
			else {
				$modelAdvert->status = Advert::STATUS_BLOCK;
			}

			if($moder_comment = Yii::app()->request->getParam('comment')) {
				$modelAdvert->moder_comment = $moder_comment;
			}

			$modelAdvert->is_moder = 0;
			$modelAdvert->moder_id = Yii::app()->user->id;
			$modelAdvert->moder_date_time_edit = date('Y-m-d H:i:s');
			$modelAdvert->save();

			$linkHtmlEdit = '<a href="'.$this->createAbsoluteUrl('objects/edit', array('id'=>$modelAdvert->id)).'">объявление</a>';
			$linkHtmlShow = '<a href="'.$this->createAbsoluteUrl('objects/show', array('id'=>$modelAdvert->getHashId())).'">объявление</a>';

			if($true == 2 && MODE_MODERATE!=3) {
				//mail модератор отклонил ваши изменения, причина $modelAdvert->moder_comment
			}
			elseif($true==1 && $params_is_edit) {
				//модератор отредактировал ваше объявление
			}
			if($true == 1 && (MODE_MODERATE!=3 || $oldStatus==Advert::STATUS_BLOCK)) {
				//mail ваше объявление прошло модерацию
				$textModer = 'Ваше '.$linkHtmlShow.' прошло модерацию';
				yii::app()->mail->sendSimple(PR_NAME_MAIL_FROM_ROBOT, $modelAdvert->user->email, 'Ваше объявление снова на сайте', $textModer);
			}
			elseif($true==0) {
				$textModer = '<p>Причина блокировки: "'.$moder_comment.'"</p>';
				$textModer .= '<p>Вы можете изменить '.$linkHtmlEdit.' для разблокировки.</p>';

				yii::app()->mail->sendSimple(PR_NAME_MAIL_FROM_ROBOT, $modelAdvert->user->email, 'Ваше объявление заблокировано', $textModer);
			}

			$this->redirect(array('objects/moderation_list',
				'ok' => 1,
			));
		}

		$this->render(DIR_VIEWS_SITE.'realty/objects/moderation_edit', array(
			'var_params'=>$params,
			'var_images_moderate'=>$images,
			'var_advert'=>$modelAdvert,
			'var_is_force'=>$is_force,
			'var_params_moder'=>$moder_new_params,
		));
	}

	public function actionModeration_list() {
		$array_sort = array(
			1=>array('date_time_create (ASC)', 'date_time_create ASC'),
			2=>array('date_time_create (DESC)', 'date_time_create DESC'),
		);
		$current_sort_type = Yii::app()->request->getQuery('sort_type', '1');

		$criteria = new CDbCriteria();
		$criteria->order= $array_sort[$current_sort_type][1];
		$criteria->addCondition('is_moder=1');

		$itemCount = Advert::model()->count($criteria);

		$pages=new CPagination($itemCount);
		$pages->route = 'objects/moderation_list';
		$pages->pageSize=10;
		$pages->applyLimit($criteria);


		$elements = Advert::model()->findAll($criteria);

		$this->render(DIR_VIEWS_SITE.'realty/objects/moderation_list', array(
			'var_pages' => $pages,
			'var_elements' => $elements,
			'var_current_sort_type' => $current_sort_type,
			'var_array_sort' => $array_sort
		));
	}

	private function eachSaveImagesAccess($images, $set_public=true, $comments='') {
		$criteria = new CDbCriteria();
		$criteria->addInCondition('id', $images);
		$criteria->addCondition('access<>' . ImageStore::ACCESS_BLOCK);// если блокированна значит ее заблокировал другой модератор
		$images_obj = ImageStore::model()->findAll($criteria);

		foreach($images_obj as $imageObj) {
			$imageObj->access = ImageStore::ACCESS_PUBLIC;
			if(!$set_public) {
				$imageObj->access = ImageStore::ACCESS_BLOCK;
			}
			if($set_public==false && $comments[$imageObj->id]) {
				$imageObj->text_moder = $comments[$imageObj->id];
			}
			$imageObj->is_moder = 0;
			$imageObj->update(array('access', 'text_moder', 'is_moder'));
		}
	}

	public function actionModeration_Images() {
		if($_POST) {
			$trueImage = Yii::app()->request->getParam('trueImageOriginal', array());

			if($trueImage) {
				$this->eachSaveImagesAccess($trueImage);
			}

			$falseComment = Yii::app()->request->getParam('falseComment');
			$array_images = Yii::app()->request->getParam('allImageOriginal');
			$diffFalse = array_diff($array_images, $trueImage);
			$this->eachSaveImagesAccess($diffFalse, false, $falseComment);

			$this->redirect(array('objects/moderation_images',
				'ok' => 1,
			));
		}

		$criteria = new CDbCriteria();
		$criteria->addCondition('is_moder=1');
		$criteria->limit=10;
		$criteria->order = 'date_time_create DESC';

		$itemCount = ImageStore::model()->count($criteria);

		$pages=new CPagination($itemCount);
		$pages->route = 'objects/moderation_images';
		$pages->pageSize=10;
		$pages->applyLimit($criteria);

		$elements = ImageStore::model()->findAll($criteria);

		$images = yii::app()->StoreFile->getUrl(\MYOBJ\appscms\core\base\SysUtils::arrvaluesmodel($elements, 'id'), StoreFile::TYPE_IMAGE,
			array('add_file' => ImageStore::TYPE_IMAGE_SMALL));
		$images_normal = yii::app()->StoreFile->getUrl(\MYOBJ\appscms\core\base\SysUtils::arrvaluesmodel($elements, 'id'), StoreFile::TYPE_IMAGE,
			array('add_file' => ImageStore::TYPE_IMAGE_NORMAL));

		$this->render(DIR_VIEWS_SITE.'realty/objects/moderation_images', array(
			'var_pages' => $pages,
			'var_images' => $images,
			'var_images_normal' => $images_normal,
		));
	}

	public function actionEdit_preshow($token) {
		$modelAdvert = false;

		if($token) {
			if(isset(Yii::app()->session['advert_edit_'.$token])==false) {
				throw new CHttpException(404, Yii::t('rl', 'page error'));
			}
		}

		$arrTokenImages = $this->getSessionToken($token, 'imagesUploadFiles');
		$arrTokenSortImages = $this->getSessionToken($token,'imagesSort');
		$arrTokenHideImages = $this->getSessionToken($token,'imagesHide');
		$arrTokenDescImages = $this->getSessionToken($token,'imagesDesc');
		$delFiles = $this->getSessionToken($token, 'delFiles');

		if($idAdvert = $this->getSessionToken($token,'obj_id')) {
			/** @var $modelAdvert Advert * */
			$modelAdvert = Advert::model()->findByPk($idAdvert);

			$this->checkMyObj($modelAdvert, 'Advert', 'edit_error_user', 'user_id', Yii::app()->user->checkAccess('admin'));

			if ($modelAdvert == false) {
				throw new CHttpException(404, Yii::t('rl', 'Obj not found'));
			}
			$type = $modelAdvert->type;
			if(!$arrTokenImages && $modelAdvert->images) {

				foreach($modelAdvert->images as $objImage) {
					$arrTokenImages[$objImage->id] = true;
				}
			}
			if($delFiles) {
				foreach($delFiles as $idDelFile) {
					unset($arrTokenImages[$idDelFile]);
				}
			}
		}
		else {
			$type = $this->getSessionToken($token,'type');
		}

		if(Yii::app()->request->getParam('save_ok')) {

			$modelAdvert = $modelAdvert ?: new Advert();
			$modelAdvert->setScenario('catalog_type_'.$type);

			//user;
			if($isNewRecord = $modelAdvert->isNewRecord) { //юзер не сможет поменять статус если например объявление было заблокированно
				$modelAdvert->user_id = Yii::app()->user->id;
				$modelAdvert->type = $type;
				$modelAdvert->status = Advert::STATUS_PUBLIC;
			}

			$newAttributes = $this->getSessionToken($token,'formAttributes');

			$is_moderator = Yii::app()->user->checkAccess('moderator') || $this->objUser->is_fiduciary;

			$is_moderate_params = 0;
			$is_moderate_images = 0;
			$is_edit_address = 0;

			//address
			$arrTokenAddress = $this->getSessionToken($token,'result_geocoder_result');
			//запись адреса только для новых
			if($arrTokenAddress && count($arrTokenAddress)>2) { // && $modelAdvert->isNewRecord
				$is_edit_address = 1;
				$modelAdvert->fix_new_country_codename = AddressAdvert::getAddressCodename($arrTokenAddress['CountryName']);

				$arrayAllConf = AddressAdvert::getArrayAddressConf();
				uasort($arrayAllConf, function($a, $b) {
					return $a[0]>$b[0];
				});

				//sql
				$lastName = '';
				$lastNameTop = '';
				$nameType = array();
				$criteria = new CDbCriteria;
				foreach($arrayAllConf as $configElem) {
					$nameField = $configElem[1];
					$typeT = $configElem[3];
					if (!isset($arrTokenAddress[$nameField])) {
						continue;
					}

					$name = $arrTokenAddress[$nameField];
					if(array_search($name.$typeT, $nameType)!==false) {
						continue;
					}
					$nameType[] = $name.$typeT;
					$codename = $name . '-' . $lastName;
					if ($typeT == AddressAdvert::TYPE_PREMISE) {
						$codename = $name . '-' . $lastNameTop;
					}

					//если район или округ называется так же как и город
					if ($name == $lastName && ($typeT==AddressAdvert::TYPE_DEPENDENTLOCALITY || $typeT==AddressAdvert::TYPE_DEPENDENTLOCALITY2)) {
						continue;
					}

					if ($typeT == AddressAdvert::TYPE_COUNTRY) {
						$codename = $name;
					}

					if (!in_array($typeT, array(
						AddressAdvert::TYPE_DEPENDENTLOCALITY,
						AddressAdvert::TYPE_DEPENDENTLOCALITY2,
						AddressAdvert::TYPE_METRO,
						AddressAdvert::TYPE_METRO_LINE,
						AddressAdvert::TYPE_PREMISE,
					))
					) {
						$lastName = $name;
						$lastNameTop = $codename;
					}

					if(isset($arrTokenAddress[$configElem[1]])) {
						$criteria->addCondition("type=".$typeT." AND codename='" . str_replace("'", "\\'", AddressAdvert::getAddressCodename($codename)) . "'", 'OR');
					}
				}
				//end sql

				$objectsAddress = AddressAdvert::model()->findAll($criteria);
				$findElemSQL = array();
				foreach($objectsAddress as $objectAddress) {
					$findElemSQL[] = array(
						'id'=>$objectAddress->id,
						'name'=>$objectAddress->name,
						'codename'=>$objectAddress->codename,
						'type'=>$objectAddress->type,
					);
				}

				$lastParentId = 0;
				$lastName = '';
				$lastNameTop = '';

				$nameType = array();
				foreach($arrayAllConf as $configElem) {
					$nameCol = $configElem[2];
					$nameField = $configElem[1];
					$typeT = $configElem[3];
					if (!isset($arrTokenAddress[$nameField])) {
						continue;
					}

					$name = $arrTokenAddress[$nameField];
					if(array_search($name.$typeT, $nameType)!==false) {
						continue;
					}
					$nameType[] = $name.$typeT;

					$sdsd =  function ($array, $elemFind) {
						$count = count($elemFind);
						foreach($array as $e) {
							$i=0;
							foreach($elemFind as $findKey => $valKey) {
								yii::log(\MYOBJ\appscms\core\base\SysUtilsString::transliterate(mb_strtolower($valKey)).'-'.\MYOBJ\appscms\core\base\SysUtilsString::transliterate(mb_strtolower($e[$findKey])), 'pr_custom', 'project');
								if(\MYOBJ\appscms\core\base\SysUtilsString::transliterate(mb_strtolower($valKey)) == \MYOBJ\appscms\core\base\SysUtilsString::transliterate(mb_strtolower($e[$findKey]))) {
									yii::log(\MYOBJ\appscms\core\base\SysUtilsString::transliterate(mb_strtolower($valKey)).'-ok-'.\MYOBJ\appscms\core\base\SysUtilsString::transliterate(mb_strtolower($e[$findKey])), 'pr_custom', 'project');
									$i++;
									if($count==$i) {
										return $e;
									}
								}
							}
						}
						return false;
					};

					$findElem = $sdsd($findElemSQL, array('name' => $name, 'type' => $typeT));
					if ($findElem) {
						$id = $findElem['id'];
						$codename = $findElem['codename'];
					}
					else {
						yii::log(print_r($findElemSQL, true), 'pr_custom', 'project');
						yii::log($name, 'pr_custom', 'project');
						yii::log($typeT, 'pr_custom', 'project');
						$modelAddress = new AddressAdvert();
						$modelAddress->parent_1 = $lastParentId;
						$modelAddress->name = $name;
						$modelAddress->type = $typeT;
						$codename = $name . '-' . $lastName;
						if ($typeT == AddressAdvert::TYPE_PREMISE) {
							$codename = $name . '-' . $lastNameTop;
						}

						//если район или округ называется так же как и город
						if ($name == $lastName && ($typeT==AddressAdvert::TYPE_DEPENDENTLOCALITY || $typeT==AddressAdvert::TYPE_DEPENDENTLOCALITY2)) {
							continue;
						}

						if ($typeT == AddressAdvert::TYPE_COUNTRY) {
							$codename = $name;
						}

						$modelAddress->codename = AddressAdvert::getAddressCodename($codename);

						$modelAddress->save();

						$id = $modelAddress->id;
					}

					if (!in_array($typeT, array(
						AddressAdvert::TYPE_DEPENDENTLOCALITY,
						AddressAdvert::TYPE_DEPENDENTLOCALITY2,
						AddressAdvert::TYPE_METRO,
						AddressAdvert::TYPE_METRO_LINE,
						AddressAdvert::TYPE_PREMISE,
					))
					) {
						$lastParentId = $id;
						$lastName = $name;
						$lastNameTop = $codename;
					}

					$newAttributes[$nameCol] = $id;
				}

				if(isset($arrTokenAddress['DistanceLocality'])) {
					$newAttributes['address_locality_length'] = (int)$arrTokenAddress['DistanceLocality'];
				}

				if(isset($arrTokenAddress['DistanceMetro1'])) {
					$newAttributes['distance_metro_1'] = (int)$arrTokenAddress['DistanceMetro1'];
				}
				if(isset($arrTokenAddress['DistanceMetro2'])) {
					$newAttributes['distance_metro_2'] = (int)$arrTokenAddress['DistanceMetro2'];
				}
				if(isset($arrTokenAddress['DistanceMetro3'])) {
					$newAttributes['distance_metro_3'] = (int)$arrTokenAddress['DistanceMetro3'];
				}
				if(isset($arrTokenAddress['DistanceMetro4'])) {
					$newAttributes['distance_metro_4'] = (int)$arrTokenAddress['DistanceMetro4'];
				}

				$newAttributes['lat'] = $arrTokenAddress['coordinates'][0];
				$newAttributes['lng'] = $arrTokenAddress['coordinates'][1];
			}

			//moderate
			$currentAttributes = $modelAdvert->getAttributes();
			$params_moder = array();
			$moderStrongParams = Advert::getModerStrongParams($type);
			if($is_moderator==false) {
				foreach ($modelAdvert->getModerAllAttributes($type) as $nameModerParam) {
					if (isset($newAttributes[$nameModerParam]) && $newAttributes[$nameModerParam] != $currentAttributes[$nameModerParam]) {
						if (MODE_MODERATE==2 && in_array($nameModerParam, $moderStrongParams)) {
							$params_moder[$nameModerParam] = $newAttributes[$nameModerParam]; //пишем только важные
							//не обновлять строгое поле
							$newAttributes[$nameModerParam] = $currentAttributes[$nameModerParam];
						}
						elseif(MODE_MODERATE==1) {
							$params_moder[$nameModerParam] = $newAttributes[$nameModerParam]; //пишем все из getModerStrongParams
						}

						if (in_array($nameModerParam, $moderStrongParams)) {
							$is_moderate_params = 1;
						}
					}
				}
			}

			if($is_moderate_params) {
				$modelAdvert->is_moder = 1; //4564563
				if(MODE_MODERATE!=3) {
					$modelAdvert->editEArray('moder_new_params', $params_moder, false);
				}

				if($modelAdvert->isNewRecord && MODE_MODERATE==1) {
					$modelAdvert->status = Advert::STATUS_BLOCK;
				}
			}

			if($is_edit_address && !($is_moderate_params && MODE_MODERATE==1)) {
				$this->clearAddress($modelAdvert);
			}

			if($modelAdvert->isNewRecord || $is_moderator) {
				$modelAdvert->setAttributes($newAttributes);
			}
			elseif(!($is_moderate_params && MODE_MODERATE==1)) {
				$modelAdvert->setAttributes($newAttributes);
			}

			if(!$is_moderator) {
				$modelAdvert->is_moder = 1; //убрать и оставить 4564563
			}
			$modelAdvert->save();
			//

			//images
			if($arrTokenImages) {
				$idsTokenImages = array_keys($arrTokenImages);
				if ($modelAdvert->images) {
					$arrOld = \MYOBJ\appscms\core\base\SysUtils::arrvaluesmodel($modelAdvert->images, 'id');
					$idsNewAddImages = array_diff($idsTokenImages, $arrOld);
				} else {
					$idsNewAddImages = $idsTokenImages;
				}

				if($idsNewAddImages) {
					$criteria = new CDbCriteria();
					$criteria->addInCondition('id', $idsNewAddImages);
					$paramsUpdate = array();
					$paramsUpdate['access'] = ImageStore::ACCESS_PUBLIC;
					if(!$is_moderator) {
						if(MODE_MODERATE!=3) {
							$paramsUpdate['access'] = ImageStore::ACCESS_MODERATE;
						}

						$paramsUpdate['is_moder'] = 1;
					}
					ImageStore::model()->updateAll($paramsUpdate, $criteria);
					$modelAdvert->UserRelated->links_edit('add', 'images', $idsNewAddImages);

					if($is_moderator==false && MODE_MODERATE!=3) {
						$is_moderate_images = 1;
					}
				}
			}

			//del files
			if($delFiles) {
				$modelAdvert->UserRelated->links_edit('remove', 'images', $delFiles);
			}

			//save images (sort, hide)
			$relemages = $modelAdvert->images;
			foreach($relemages as $k=>$v) {
				$array_edit_images = array();
				if(isset($arrTokenHideImages[$k]) && $arrTokenHideImages[$k]!='') {
					$array_edit_images['is_hide'] = $arrTokenHideImages[$k];
				}
				//для select почему то не передаются если не трогал
				if(isset($arrTokenSortImages[$k])) {
					$array_edit_images['sort'] = $arrTokenSortImages[$k];
				}
				if(isset($arrTokenDescImages[$k])) {
					$array_edit_images['desc'] = $arrTokenDescImages[$k];
				}

				if($array_edit_images) {
					ImageStore::model()->updateByPk($k, $array_edit_images);
				}
			}

			unset(Yii::app()->session['advert_edit_'.$token]);

			$isSphinxParams = 0;

			//в объявлении были параметры которые strong или добавлялись новые изображения
			if($is_moderator==false && ($is_moderate_params || $is_moderate_images || MODE_MODERATE==3)) {
				if(MODE_MODERATE==1) {
					//mail пользователю - некоторые параметры вашего объявления на модерации
				}
				//mail модераторам - изменения в объявлении
				yii::app()->mail->sendSimple(PR_NAME_MAIL_FROM_ROBOT, PR_ADMIN_EMAIL, 'Изменение объявления' . ' ' . PR_SITE_NAME, $this->createAbsoluteUrl('objects/show/?id=' . $modelAdvert->getHashId()));
			}

			if($modelAdvert->is_edit_field_sphinx) {
				$isSphinxParams = 1;
			}

			$this->setCookie('site_id', $modelAdvert->site_id, false, false, 'fse3df');

			$this->redirect(array('objects/edit_success',
				'o'=>$modelAdvert->id,
				'm_p' => $is_moderate_params,
				'm_i' => $is_moderate_images,
				'n'=>(int)$isNewRecord,
				's'=>$isSphinxParams,
			));
		}
		else {
			$countryName = AddressAdvert::getAddressCodename($this->getSessionToken($token,'result_geocoder_result')['CountryName']);
			if(!$countryName) {
				$countryName = $modelAdvert->objCountry->codename;
			}

			$this->render(DIR_VIEWS_SITE.'realty/objects/preshow', array(
				'var_countryName'=>$countryName,
				'var_paramsForm'=>$this->getSessionToken($token,'formAttributes'),
				'var_type'=>$this->getSessionToken($token,'type'),
				'var_advert'=>$modelAdvert,
				'var_token'=>$token,
				'var_arrTokenImages'=>$arrTokenImages,
				'var_arrTokenSortImages'=>$arrTokenSortImages,
			));
		}
	}

	protected static function arrayDefAddress() {
		return array('text'=>'', 'CountryName'=>'');
	}

	public function actionAdd() {
		$this->render(DIR_VIEWS_SITE.'realty/objects/add', array(

		));
	}

	public function actionEdit($type=null,$id=null) {
		if($type) {
			$namesType = array_flip(\MYOBJ\appscms\core\business\realty\CategoryEnumeration::fieldsTransliterate());
			$type = $namesType[$type];
		}

		$getPost = Yii::app()->request->getPost(NAME_DF);
		$token=null;
		if(isset($getPost['token']) && $this->getSessionToken($getPost['token'], 'type')) {
			$token = $getPost['token'];
			$type = $this->getSessionToken($token, 'type');
		}

		$current_object = false;
		$current_uploadImages = array();
		$current_attributes = array();
		$current_address_str = '';
		$main_image = '';
		$current_imagesSort = array();
		$current_imagesHide = array();
		$current_imagesDesc = array();

		if($token) {
			if(isset(Yii::app()->session['advert_edit_'.$token])==false) {
				throw new CHttpException(404, Yii::t('rl', 'page error'));
			}
		}

		if($id || ($token && ($id = $this->getSessionToken($token,'obj_id')))) {
			/** @var $current_object Advert * */
			$current_object = $this->checkMyObj($id, 'Advert', 'edit_error_user', 'user_id', Yii::app()->user->checkAccess('admin'));
			$type = $current_object->type;
		}

		if($token) {
			$type = $this->getSessionToken($token,'type');
			$current_uploadImages = $this->getSessionToken($token,'imagesUploadFiles');
			$current_attributes = $this->getSessionToken($token,'formAttributes');
			$current_address_str = $this->getSessionToken($token,'result_geocoder_result')['text'];
			$main_image = $this->getSessionToken($token,'formAttributes')['default_image_id'];
			$current_imagesSort = $this->getSessionToken($token,'imagesSort');
			$current_imagesHide = $this->getSessionToken($token,'imagesHide');
			$current_imagesDesc = $this->getSessionToken($token,'imagesDesc');
		}
		if($current_object) {
			if($current_objectImages = $current_object->images) {
				//main image
				if(!$main_image) {
					$main_image = $current_object->default_image_id;
				}
				//images
				if (!$current_uploadImages) {
					foreach($current_objectImages as $k=>$v) {
						$current_uploadImages[$k] = $v->md5;
					}
				}
				//sort
				if(!$current_imagesSort) {
					foreach($current_objectImages as $v) {
						$current_imagesSort[$v->id] = $v->sort;
					}
				}
				//hide
				if(!$current_imagesHide) {
					foreach($current_objectImages as $v) {
						if($v->is_hide) {
							$current_imagesHide[$v->id] = $v->is_hide;
						}
					}
				}
				//desc
				if(!$current_imagesDesc) {
					foreach($current_objectImages as $v) {
						$current_imagesDesc[$v->id] = $v->desc;
					}
				}
			}

			//attributes
			if(!$current_attributes) {
				$current_attributes = $current_object->getAttributes();
			}

			//address
			if(!$current_address_str) {

				$current_address_str = '';
				foreach($this->showReqAddressAdvert($current_object) as $elemAddress) {
					$current_address_str .= ' '.$elemAddress->name;
				}
			}
		}

		$countryName = false;
		if(isset($getPost['token'])) {
			if($this->getSessionToken($getPost['token'], 'result_geocoder_result') && $this->getSessionToken($getPost['token'], 'result_geocoder_result')['CountryName']) {
				$countryName = AddressAdvert::getAddressCodename($this->getSessionToken($getPost['token'], 'result_geocoder_result')['CountryName']);
			}
			elseif(isset($getPost['country_codename']) && $getPost['country_codename']) {
				$countryName = $getPost['country_codename'];
			}
		}

		if(!$countryName && $current_object) {
			$countryName = $current_object->objCountry->codename;
		}

		if(!$countryName) { //можно потом заменить на куку что бы всегда не вставала россия у того кто работает в таиланде например
			$countryName = 'rossiya';
		}

		//form
		$form = \MYOBJ\appscms\core\base\form\DForm::create();
		//AR Advert
		$form->setScenario('catalog_type_' . $type);
		$form->addRulesAR(Advert::getAllRules($type, $countryName));
		$form->addAttributeLabelAR(Advert::UserAttributeLabels($type));

		$form->addAttributeRule('token', 'required', $token);

		$form->addAttributeRule('site_id', array('exist', 'className'=>'SocialPublicPage', 'attributeName'=>'id',
			'criteria' => array(
				'with'=>array('moderators'),
				'condition'=>'t.user_id=\''.$this->objUser->id.'\' || moderators_moderators.user_id='.$this->objUser->id)));
		$form->addAttributeLabel('site_id', Yii::t('rl', 'от имени сайта'));
		if($getCookieSite = $this->getCookie('site_id', 'fse3df')) {
			$form->site_id = $getCookieSite;
		}

		$data_county = array('rossiya'=>Yii::t('rl', 'Russia'), 'other'=>'---'.Yii::t('rl', 'as_other').'---');
		$form->addAttributeRule('country_codename', array('safe'));
		$form->addAttributeLabel('country_codename', Yii::t('rl', 'country'));

		$form->addAttributeRule('address', 'required');
		$form->addAttributeLabel('address', Yii::t('rl', 'address'));
		$form->addAttributeRule('default_image_id', 'safe');

		$form->addAttributeRule('image', 'safe');
		$form->addAttributeLabel('image', Yii::t('rl', 'Image'));

		$form->addAttributeRule('is_hide', 'safe');

		//init params нет тикина нзачит заходит впервые
		if(!$token) {
			$form->token = mt_rand();

			if($current_object) {
				$form->setAttributes($current_attributes);
				$form->address = $current_address_str;
				$this->setSessionToken($form->token, 'obj_id', $id);
				$this->setSessionToken($form->token, 'imagesUploadFiles', $current_uploadImages);
				if(!$token) {
					$this->setSessionToken($form->token, 'oldCountryName', $current_object->objCountry->name);
				}
			}
		}
		else {
			$form->setAttributes($current_attributes);
		}

		if($getPost) {
			$form->setAttributes($getPost);

			//total_area replace comma
			if(property_exists($form, 'total_area')) {
				$form->total_area = str_replace(',', '.', $form->total_area);
			}
			if(property_exists($form, 'land_area')) {
				$form->land_area = str_replace(',', '.', $form->land_area);
			}
		}

		$form->country_codename = $countryName;
		if(!isset($data_county[$countryName])) {
			$form->country_codename = 'other';
		}

		if($delFiles = $this->getSessionToken($form->token, 'delFiles')) {
			foreach($delFiles as $k) {
				unset($current_uploadImages[$k]);
			}
		}

		if($getPost && !Yii::app()->request->getParam('is_back')) {
			if(!$token) {
				if ($this->getSessionToken($form->token, 'imagesUploadFiles')) {
					$current_uploadImages = $this->getSessionToken($form->token, 'imagesUploadFiles');
				}

				if ($this->getSessionToken($form->token, 'result_geocoder_result')['text']) {
					$current_address_str = $this->getSessionToken($form->token, 'result_geocoder_result')['text'];
				}

				if ($this->getSessionToken($form->token, 'imagesSort')) {
					$current_imagesSort = $this->getSessionToken($form->token, 'imagesSort');
				}

				if ($this->getSessionToken($form->token, 'imagesDesc')) {
					$current_imagesDesc = $this->getSessionToken($form->token, 'imagesDesc');
				}
			}

			$main_image = $form->default_image_id;

			$current_imagesHide = $form->is_hide;

			if (!$form->address || (!$current_address_str && !$this->getSessionToken($form->token, 'result_geocoder_result')['text'])) {
				$current_address_str = $form->address = '';
				$this->setSessionToken($form->token, 'result_geocoder_result', static::arrayDefAddress());
			}
			else {
				$newCountryName = $this->getSessionToken($form->token, 'result_geocoder_result')['CountryName'];
				$oldCountryName = $this->getSessionToken($form->token, 'oldCountryName');
				if($newCountryName && $newCountryName != $oldCountryName) {
					if($newCountryName=='Россия') {
						$form->currency = 0;
					}
					else {
						$form->currency = '';
					}

					$this->setSessionToken($form->token, 'oldCountryName', $newCountryName);
				}
			}

			if ($form->validate()) {

				$this->setSessionToken($form->token, 'formAttributes', $form->getAttributes());
				$this->setSessionToken($form->token, 'imagesHide', $form->is_hide);
				$this->setSessionToken($form->token, 'type', $type);

				$this->redirect(array('objects/edit_preshow',
					'token' => $form->token,
				));
			}
		}

		$this->render(DIR_VIEWS_SITE.'realty/objects/edit', array(
			'var_modelForm'=>$form,
			'var_countryName' => $countryName,
			'var_type'=>$type,
			'var_current_uploadImages' => $current_uploadImages,
			'var_current_address' => $current_address_str,
			'var_is_new' => $id!=true,
			'var_main_image' => $main_image,
			'var_images_sort' => $current_imagesSort,
			'var_images_hide' => $current_imagesHide,
			'var_images_desc' => $current_imagesDesc,
			'var_current_object' => $current_object,
			'var_data_county' => $data_county,
		));
	}

	public function actionEdit_success($o, $m_p, $m_i, $n) {
		$obj = $this->checkMyObj($o, 'Advert', 'edit_success', 'user_id', Yii::app()->user->checkAccess('admin'));
		$this->render(DIR_VIEWS_SITE.'realty/objects/edit_success', array(
			'var_moder_params'=>$m_p,
			'var_moder_images'=>$m_i,
			'var_n'=>$n,
			'var_object'=>$obj,
		));
	}

	protected function showReqAddressAdvert($objAdvert, $is_sphinx=false) {
		if($is_sphinx) {
			$objAdvert = Advert::createSphinxObject($objAdvert);
		}

		$array_address_fields = array(
			'address_country_id',
			'address_administrative_id',
			'address_dependentlocality_id',
			'address_dependentlocality2_id',
			'address_locality_id',
			'address_metro_line1_id',
			'address_metro1_id',
			'address_metro_line2_id',
			'address_metro2_id',
			'address_metro_line3_id',
			'address_metro3_id',
			'address_metro_line4_id',
			'address_metro4_id',
			'address_thoroughfare_id',
			'address_premise_id',
		);

		$inId = array();
		foreach($array_address_fields as $nameField) {
			if($objAdvert->$nameField) {
				$inId[] = $objAdvert->$nameField;
			}
		}

		return AddressAdvert::model()->findAll(array('condition'=>'id IN ('.implode(',', $inId).')'));
	}

	public function actionShow($id) {
		$pk = substr($id, 0, -5);
		$hash = substr($id, -5);

		$objAdvert = Advert::model()->findByPk($pk);
		$isErrorHash = false;
		if($objAdvert && strcmp($objAdvert->hash, $hash) !== 0) {
			$isErrorHash = true;
			//заблокировать юзера капчей
			$this->setBlockIp('show', 60*3);
		}

		if(!$objAdvert || $objAdvert->status!=Advert::STATUS_PUBLIC || $isErrorHash) {
			$this->render(DIR_VIEWS_SITE.'realty/other/def_page', array(
				'var_header'=>'Объявление не найденно',
				'var_html'=>'<div class="alert alert-danger">'.yii::t('rl', 'Объявление еще не созданно или оно было удаленно').'</div>',
			));
			yii::app()->end();
		}

		$this->render(DIR_VIEWS_SITE.'realty/objects/show', array(
			'var_objAdvert'=>$objAdvert,
		));
	}

	/**
	 * @param $type
	 */
	public function actionList() {
		$is_sphinx = true;

		$elements_map_one = Yii::app()->request->getParam('i_elem_map_click')?explode(',', Yii::app()->request->getParam('i_elem_map_click')):false;

		//check type
		$getType = Yii::app()->request->getParam('type', 'flat');
		$namesType = array_flip(\MYOBJ\appscms\core\business\realty\CategoryEnumeration::fieldsTransliterate());
		if(isset($namesType[$getType])) {
			$type = $namesType[$getType];
		}
		else {
			//путь будут ошибки
			throw new CHttpException(404, Yii::t('rl', 'page error type'));
			//$this->setSessionArray('custom_error_message', array('error type', 0));
		}

		//check loc
		$this->blockRobotLocation(Yii::app()->request->getParam('location'));

		$objLoc = AddressAdvert::findOne(Yii::app()->request->getParam('location'));
		if(!$objLoc) {
			//не менять поведение!!!
			//сделал ошибку что бы если поисковик зайдет по корявому адресу что бы он считал этот адрес недействительным
			throw new CHttpException(404, Yii::t('rl', 'page error location'));
		}
		//
		if(Yii::app()->request->getParam('list_type')=='map' && !in_array($objLoc->type, array(AddressAdvert::TYPE_COUNTRY, AddressAdvert::TYPE_ADMINISTRATIVE, AddressAdvert::TYPE_LOCALITY))) {
			throw new CHttpException(404, Yii::t('rl', 'robot no page map'));
		}
		//
		$this->setCookie('current_location', $objLoc->id, false, false, 'fse3df');
		//
		$array_top_location = AddressAdvert::recAddress($objLoc);
		$countryObj = $array_top_location[0];

		$confTypesFilter = Advert::confTypesFilter($type, $countryObj->codename);

		//params
		$formCheckParams = \MYOBJ\appscms\core\base\form\DForm::create();
		$rulesTypeParams = Advert::rulesCatalogTypesFilter($type, $countryObj->codename);
		$formCheckParams->addRulesAR($rulesTypeParams);
		$formCheckParams->addAttributeLabelAR(Advert::UserAttributeLabels($type, true));

		//custom params
		$formCheckParams->addAttributeRule('old_type', array('safe'));
		$formCheckParams->addAttributeRule('old_country', array('safe'));
		$formCheckParams->addAttributeRule('type', array('required'));
		$formCheckParams->addAttributeRule('location', array('required'));
		$formCheckParams->addAttributeRule('min_metro', array('numerical'));
		$formCheckParams->addAttributeLabel('min_metro', Yii::t('rl', 'meters from metro'));
		$formCheckParams->addAttributeRule('is_photo', array('boolean'));
		$formCheckParams->addAttributeLabel('is_photo', Yii::t('rl', 'with photo'));
		$formCheckParams->addAttributeRule('is_video', array('boolean'));
		$formCheckParams->addAttributeLabel('is_video', Yii::t('rl', 'with video'));
		$formCheckParams->addAttributeRule('site', array('safe'));
		$formCheckParams->addAttributeRule('user', array('safe'));
		$formCheckParams->addAttributeRule('convert_currency', array('numerical'));
		$formCheckParams->addAttributeRule('show_currency', array('numerical'));
		$formCheckParams->addAttributeRule('list_type', array('in', 'range'=>array('list', 'list_small', 'map')));
		$array_sort = array(
			'df'=>array('по умолчанию', 'top_date_time DESC'),
			'dd'=>array('по дате (новые)', 'date_time_edit DESC'),
			'da'=>array('по дате (старые)', 'date_time_edit ASC'),
			'pd'=>array('по цене (дороже)', static::COL_PRICE.' DESC'),
			'pa'=>array('по цене (дешевле)', static::COL_PRICE.' ASC'),
			'ld'=>array('ближе к центру', 'address_locality_length ASC'),
			'la'=>array('дальше от центра', 'address_locality_length DESC'),
		);
		if(1) { //пока для всех городов
			$array_sort['md'] = array('ближе к метро', 'distance_metro_1 ASC');
			$array_sort['ma'] = array('дальше от метро', 'distance_metro_1 DESC');
		}

		$formCheckParams->addAttributeRule('sort_type', array('in', 'range'=>array_keys($array_sort)));
		$formCheckParams->addAttributeRule('sort_type2', array('in', 'range'=>array_keys($array_sort)));
		$formCheckParams->addAttributeRule('addit', array('safe'));

		$formCheckParams->addAttributeRule('geo', array('safe'));
		$formCheckParams->addAttributeRule('geo_map', array('safe'));

		$formCheckParams->setAttributes($_GET);
		$formCheckParams->setScenario('catalog_type_'.$type);
		if(!$formCheckParams->validate() && !Yii::app()->request->isAjaxRequest) {
			$this->setSessionArray('custom_error_message', array(current($formCheckParams->getErrors())[0], 0));
			$formCheckParams->unsetAttributes(array_keys($formCheckParams->getErrors()));
		}

		if(
			($formCheckParams->old_country && $formCheckParams->old_country!=$countryObj->codename)
			||
			($formCheckParams->old_type && $formCheckParams->old_type!=$formCheckParams->type)
		) {
			$array = array('type'=>$formCheckParams->type, 'location'=>$formCheckParams->location);
			if($formCheckParams->list_type) {
				$array['list_type'] = $formCheckParams->list_type;
			}
			$this->redirect($this->createUrl(Yii::app()->request->pathInfo, $array));
		}

		$def_convert_currency = Advert::CURRENCY_EUR;
		$countryCurrencyList = Advert::getCountryCurrencyListAll();
		if(isset($countryCurrencyList[$countryObj->codename])) {
			$addcountryCurrency = $countryCurrencyList[$countryObj->codename];
			$def_convert_currency = $addcountryCurrency[0];
		}

		if($formCheckParams->convert_currency=='') {
			$formCheckParams->convert_currency = $def_convert_currency;
		}

		$def_show_currency = $def_convert_currency;
		if($formCheckParams->show_currency=='') {
			$formCheckParams->show_currency = $def_show_currency;
		}

		$def_is_price_for_all = Advert::FILTER_SUB_TYPE_TYPE_AREA_ALL;
		if(property_exists($formCheckParams, 'is_price_for_all') && $formCheckParams->is_price_for_all=='') {
			$formCheckParams->is_price_for_all = $def_is_price_for_all;
		}
		$def_type_area = Advert::TYPE_AREA_AR;
		if(property_exists($formCheckParams, 'type_area') && $formCheckParams->type_area=='') {
			$formCheckParams->type_area = $def_type_area;
		}

		//check list_type
		if(!$formCheckParams->list_type) {
			$formCheckParams->list_type = 'list';
		}
		//check sort_type
		if(!$formCheckParams->sort_type) {
			$formCheckParams->sort_type = 'df';
		}
		if($formCheckParams->sort_type2 && $formCheckParams->sort_type == $formCheckParams->sort_type2) {
			$formCheckParams->sort_type2 = '';
		}

		$sqlSelectSphinx = '';
		$criteria = new CDbCriteria();

		//type
		$criteria->addCondition('type='.$type);

		//geo
		$strGeo = false;
		if($formCheckParams->geo_map) {
			$all_elem_geo = CJSON::decode($formCheckParams->geo_map);
			$strGeo = '[['.$all_elem_geo[0][0].','.$all_elem_geo[0][1].'],['.$all_elem_geo[0][0].','.$all_elem_geo[1][1].'],['.$all_elem_geo[1][0].','.$all_elem_geo[1][1].'],['.$all_elem_geo[1][0].','.$all_elem_geo[0][1].']]';
		}
		elseif($formCheckParams->geo) {
			$strGeo = $formCheckParams->geo;
			if(substr_count($strGeo, ',')<4) {
				$formCheckParams->addError('geo', yii::t('rl', 'You specified less than 3 points'));
				$strGeo = false;
			}
		}

		if($strGeo) {
			//40.95164274496,-76.88583678218,41.188446201688,-73.203723511772,39.900666261352,-74.171833538046,40.059260979044,-76.301076056469
			//[[55.779859024960885,37.36795818847655],[55.62087478035437,37.466835141601564],[55.719443997724376,37.943367124023425],[55.89809270372365,37.82938396972656]]

			if($is_sphinx) {
				$strGeo = str_replace(array('[[', ']]'), '', $strGeo);
				$strGeo = str_replace('],[', ',', $strGeo);
				$sqlSelectSphinx .= ($sqlSelectSphinx?',':'').'CONTAINS(GEOPOLY2D('.$strGeo.'),lat,lng) AS inside_geo';
				$criteria->addCondition('inside_geo=1');
			}
			else {
				//$strGeoAr = '';
				//$all_elem_geo = CJSON::decode($strGeo);
				//array_pop($all_elem_geo);
				//array_push($all_elem_geo, $all_elem_geo[0]);
				//foreach($all_elem_geo as $elGeo) {
					//$strGeoAr .= '('.$elGeo[0].' '.$elGeo[1].'),';
				//}
				//$strGeoAr = substr($strGeoAr,0,-1);
				$strGeo = str_replace(array('[[', ']]'), '', $strGeo);
				$strGeo = str_replace('],[', 's', $strGeo);
				$strGeo = str_replace(',', ' ', $strGeo);
				$strGeo = '('.str_replace('s', '),(', $strGeo).')';
				yii::app()->db->createCommand("set @r = GeomFromText('Polygon(".$strGeo.")')")->execute();
				$criteria->addCondition("contains(@r,GeomFromText(CONCAT('POINT(',lat,' ',lng,')'))) = 1");
			}
		}

		$find_metro_sql = '';
		//location
		if(Yii::app()->request->getParam('is_no_use_location')!=1) {
			$fieldLocation = \MYOBJ\appscms\core\base\SysUtilsArray::find_arr_params(AddressAdvert::getArrayAddressConf(), array('3'=>$objLoc->type))[2];
			if($objLoc->type==AddressAdvert::TYPE_METRO) {
				$find_metro_sql = 'IF(address_metro1_id='.$objLoc->id.',1,
				IF(address_metro2_id='.$objLoc->id.',1,
				IF(address_metro3_id='.$objLoc->id.',1,
				IF(address_metro4_id='.$objLoc->id.',1,0))))';
			}
			elseif($objLoc->type==AddressAdvert::TYPE_METRO_LINE) {
				$find_metro_sql = 'IF(address_metro_line1_id='.$objLoc->id.',1,
				IF(address_metro_line2_id='.$objLoc->id.',1,
				IF(address_metro_line3_id='.$objLoc->id.',1,
				IF(address_metro_line4_id='.$objLoc->id.',1,0))))';
			}

			if($find_metro_sql) {
				if($is_sphinx) {
					$nameProp = 'find_metro';
				}
				else {
					$nameProp = $find_metro_sql;
				}
				$criteria->addCondition($nameProp.'=1');
			}
			else {
				$criteria->addCondition($fieldLocation . '=' . $objLoc->id);
			}
		}

		//min_metro
		if($formCheckParams->min_metro) {
			$criteria->addCondition('distance_metro_1>0 AND distance_metro_1<'.$formCheckParams->min_metro);
		}
		//is_photo
		if($formCheckParams->is_photo) {
			$criteria->addCondition('default_image_id>0');
		}
		//is_video
		if($formCheckParams->is_video) {
			if($is_sphinx) {
				$criteria->addCondition('youtube_links=1');
			}
			else {
				$criteria->addCondition("youtube_links<>''");
			}
		}
		//site
		if($formCheckParams->site) {
			$find_community = AbsSocialCommunity::getObjName($formCheckParams->site);
			if($find_community) {
				$criteria->addCondition('site_id=' . $find_community['id']);
			}
			else {
				$formCheckParams->site = '';
			}
		}
		//user
		if($formCheckParams->user) {
			$find_community = AbsSocialCommunity::getObjName($formCheckParams->user);
			if($find_community) {
				$criteria->addCondition('user_id=' . $find_community['id']);
			}
			else {
				$formCheckParams->user = '';
			}
		}

		$name_total_area = 'total_area';
		if($type==CategoryEnumeration::LAND || $type==CategoryEnumeration::LAND_RENT || $type==CategoryEnumeration::COUNTRY_LAND) {
			$name_total_area = 'land_area';
		}

		//convert_currency
		$allDataСurrency = Advert::getCurrencyList();
		$all_course = Advert::getCurrencyCourseList();
		$get_cours_convert = function($cur1, $cur2) use($all_course) {
			if($cur1==$cur2) return 1;
			return $all_course[$cur1.$cur2];
		};
		$sqlPrice = static::COL_PRICE;
		if(property_exists($formCheckParams, 'is_price_for_all')) {
			$sqlPrice = 'IF(is_price_for_all=' . Advert::FILTER_SUB_TYPE_TYPE_AREA_ONE . ' AND ' . $formCheckParams->is_price_for_all . '=' . Advert::FILTER_SUB_TYPE_TYPE_AREA_ALL . ',' . static::COL_PRICE . ' * '.$name_total_area.', IF(is_price_for_all=' . Advert::FILTER_SUB_TYPE_TYPE_AREA_ALL . ' AND ' . $formCheckParams->is_price_for_all . '=' . Advert::FILTER_SUB_TYPE_TYPE_AREA_ONE . ', ' . static::COL_PRICE . ' / '.$name_total_area.', ' . static::COL_PRICE . '))';
			if (static::COL_PRICE == 'price_one') {
				$sqlPrice = 'price_one';
			}
		}
		//currency
		if($formCheckParams->convert_currency!='') {
			$find_currency_name = $allDataСurrency[$formCheckParams->convert_currency];
			$sqlPrice = $sqlPrice.'*(
			IF(currency='.Advert::CURRENCY_RUB.','.$get_cours_convert('RUB', $find_currency_name).',
			IF(currency='.Advert::CURRENCY_USD.','.$get_cours_convert('USD', $find_currency_name).',
			IF(currency=2,'.$get_cours_convert('EUR', $find_currency_name).',
			IF(currency=3,'.$get_cours_convert('KZT', $find_currency_name).',
			IF(currency=4,'.$get_cours_convert('BYN', $find_currency_name).',
			IF(currency=5,'.$get_cours_convert('UAH', $find_currency_name).',0)))))))';
		}

		//type_area
		$all_type_area = Advert::getTotalAreaList();
		$get_type_area_convert = function($p1, $p2) use($all_type_area) {
			if($p1==$p2) return 1;
			return $all_type_area[$p1.$p2];
		};
		$land_area_sql = '';
		if(property_exists($formCheckParams, 'type_area') && $formCheckParams->type_area!='') {
			$land_area_sql = 'land_area*(
			IF(type_area='.Advert::TYPE_AREA_KV_M.','.$get_type_area_convert(Advert::TYPE_AREA_KV_M, $formCheckParams->type_area).',
			IF(type_area='.Advert::TYPE_AREA_AR.','.$get_type_area_convert(Advert::TYPE_AREA_AR, $formCheckParams->type_area).',
			IF(type_area='.Advert::TYPE_AREA_ACRE.','.$get_type_area_convert(Advert::TYPE_AREA_ACRE, $formCheckParams->type_area).',
			IF(type_area='.Advert::TYPE_AREA_HECTARE.','.$get_type_area_convert(Advert::TYPE_AREA_HECTARE, $formCheckParams->type_area).',0)))))';
		}

		$funcReplaceMATCH = function($obj, $nameProp) {
			$regRepl = '/[:;\*\^&\+\|@\-\!\\\'\[\]\(\)\{\}\?%<>.,=~]/'; // или использовать функцию quotemeta
			$obj->$nameProp =    preg_replace($regRepl, '', $obj->$nameProp);
		};
		$array_MATCH = array();
		$is_val_price = false;
		foreach($confTypesFilter as $nameF=>$confF) {
			if(!$formCheckParams->getError($nameF) && $formCheckParams->$nameF!='') {
				$nameProp = $confF[0];

				if($nameProp=='price') {
					$is_val_price = true;

					//&& $formCheckParams->convert_currency
					if($is_sphinx) {
						$nameProp = 'convert_price';
					}
					else {
						$nameProp = $sqlPrice;
					}
				}

				if($land_area_sql && $nameProp=='land_area') {
					if($is_sphinx) {
						$nameProp = 'convert_land_area';
					}
					else {
						$nameProp = $land_area_sql;
					}
				}
				if($nameProp=='type_area' || $nameProp=='is_price_for_all') {
					continue;
				}

				if($confF[1]==ADVERT::TYPE_FILER_PARAM_MIN || $confF[1]==ADVERT::TYPE_FILER_SELECT_MIN) {
					$criteria->addCondition($nameProp.'>='.$formCheckParams->$nameF);
				}
				elseif($confF[1]==ADVERT::TYPE_FILER_PARAM_MAX || $confF[1]==ADVERT::TYPE_FILER_SELECT_MAX) {
					$criteria->addCondition($nameProp.'<='.$formCheckParams->$nameF);
				}
				elseif($confF[1]==ADVERT::TYPE_FILER_PARAM_IN) {
					if($is_sphinx) {
						$criteria->addCondition($nameProp.' IN ('.implode(',', $formCheckParams->$nameF).')');
					}
					else {
						$criteria->addInCondition($nameProp, $formCheckParams->$nameF);
					}
				}
				elseif($confF[1]==ADVERT::TYPE_FILER_PARAM_FULLTEXT) {
					if($is_sphinx && in_array($nameF, Advert::getPropertySphinx(true))) {
						$funcReplaceMATCH($formCheckParams, $nameF);
						$sphinx_find = '@'.$nameF.' '.$formCheckParams->$nameF;

						$ex_name = $nameF.'_ex';
						if(property_exists($formCheckParams, $ex_name) && $formCheckParams->$ex_name!='') {
							$funcReplaceMATCH($formCheckParams, $ex_name);
							$sphinx_find .= ' !'.$formCheckParams->$ex_name;
						}

						$array_MATCH[] = $sphinx_find;
					}
					elseif(!in_array($nameProp, Advert::getPropertySphinx(true))) {
						$criteria->addCondition($nameProp . "='" . $formCheckParams->$nameF . "'");
					}
				}
				elseif($confF[1]==ADVERT::TYPE_FILER_PARAM_STRING) {
					$criteria->addCondition($nameProp."='".$formCheckParams->$nameF."'");
				}
				//TYPE_FILER_PARAM_NUMERIC
				else {
					$criteria->addCondition($nameProp.'='.$formCheckParams->$nameF);
				}
			}
		}
		if($array_MATCH) {
			$criteria->addCondition("MATCH('".implode(' ', $array_MATCH)."')");
		}
		//
		if($is_sphinx) {
			if($is_val_price) {
				$sqlSelectSphinx .= ($sqlSelectSphinx?',':'').'ceil('.$sqlPrice . ') AS convert_price';
			}

			if($land_area_sql) {
				$sqlSelectSphinx .= ($sqlSelectSphinx?',':'').'ceil('.$land_area_sql . ') AS convert_land_area';
			}

			if($find_metro_sql) {
				$sqlSelectSphinx .= ($sqlSelectSphinx?',':'').$find_metro_sql . ' AS find_metro';
			}
		}

		//sort
		$criteria->order=$array_sort[$formCheckParams->sort_type][1];
		//sort2
		if($formCheckParams->sort_type2 && $formCheckParams->sort_type != $formCheckParams->sort_type2) {
			$criteria->order .= ', '.$array_sort[$formCheckParams->sort_type2][1];
		}

		if($elements_map_one) {
			$criteria->condition = '';
			$criteria->params = array();

			$criteria = new CDbCriteria();
			$idsHash = array();
			foreach ($elements_map_one as $oi) {
				$idsHash[substr($oi, 0, -5)] = substr($oi, -5);
			}
			$criteria->addCondition('id IN ('.implode(',', array_keys($idsHash)).')');
		}

		$criteria->addCondition('status=' . Advert::STATUS_PUBLIC);

		/** @var CDbConnection $dbSphinxQL */
		$dbSphinxQL = yii::app()->dbSphinxQL;

		$itemCount = 0;
		if($this->isReloadAll==false) {
			if ($is_sphinx) {
				$itemCount = $dbSphinxQL->createCommand()
					->select('count(*)' . ($sqlSelectSphinx ? ',' : '') . $sqlSelectSphinx)
					->from('main')
					->where($criteria->condition, $criteria->params)
					->group($criteria->group)
					->having($criteria->having)
					->queryScalar();
			} else {
				$itemCount = Advert::model()->count($criteria);
			}
		}

		$pages=new CPagination($itemCount);
		$pages->route = 'objects/list';
		$pages->pageSize=YII_DEBUG?3:10;
		if($elements_map_one) {
			$pages->pageSize=100;
		}
		$pages->applyLimit($criteria);

		//код ниже именно в этом месте т.к $pages->applyLimit меняет limit
		if(!Yii::app()->request->getParam('is_pager_map') && ($formCheckParams->geo_map || Yii::app()->request->getParam('first_load_map'))) {
			$criteria->limit = static::MAX_GEO_LIMIT;
		}

		$elements = array();
		if($itemCount) {
			if ($is_sphinx) {
				$elements = $dbSphinxQL->createCommand()
					->select('*' . ($sqlSelectSphinx ? ',' : '') . $sqlSelectSphinx)
					->from('main')
					->where($criteria->condition)
					->group($criteria->group)
					->having($criteria->having)
					->order($criteria->order)
					->limit($criteria->limit)
					->offset($criteria->offset)
					->queryAll();
			} else {
				$elements = Advert::model()->findAll($criteria);
			}
		}

		//params save list
		$list_clear_params = UserSaveAdvertList::clearSaveListParams($formCheckParams->getAttributes(), $type, $countryObj->codename);

		//убрать значения по умолчанию - это исключит дубли в seo и ненужные поля в save list пользователя
		if($list_clear_params['convert_currency']==$def_convert_currency) {
			unset($list_clear_params['convert_currency']);
		}
		if(isset($list_clear_params['show_currency']) && $list_clear_params['show_currency']==$def_show_currency) {
			unset($list_clear_params['show_currency']);
		}
		if(isset($list_clear_params['is_price_for_all']) && $list_clear_params['is_price_for_all']==$def_is_price_for_all) {
			unset($list_clear_params['is_price_for_all']);
		}
		if(isset($list_clear_params['type_area']) && $list_clear_params['type_area']==$def_type_area) {
			unset($list_clear_params['type_area']);
		}

		//ajax scroll
		if(Yii::app()->request->isAjaxRequest || $elements_map_one) {
			$this->layout = false;
			$this->renderPartial(DIR_VIEWS_SITE.'realty/objects/list_elements_pages', array(
				'var_pages' => $pages,
				'var_elements' => $elements,
				'var_is_sphinx' => $is_sphinx,
				'var_formCheckParams' => $formCheckParams,
				'get_cours_convert' => $get_cours_convert,
				'var_allDataСurrency' => $allDataСurrency,
				'var_name_total_area' => $name_total_area,
				'var_get_type_area_convert' => $get_type_area_convert,
				'var_countryName' => $countryObj->codename,
			));
			yii::app()->end();
		}

		array_walk($array_sort, function(&$item1, $key) use($array_sort) {
			$item1=$array_sort[$key][0];
		});

		$array_types = \MYOBJ\appscms\core\business\realty\CategoryEnumeration::fieldsArrayType();
		$array_types_translit = array();
		foreach($array_types as $k=>$v) {
			$array_types_translit[$k] = array(yii::t('rl', $v, 1), yii::t('rl', $v, 2));
		}

		$type_d = yii::t('rl', 'Buy_seo');
		$type_t = $array_types_translit[$formCheckParams->type][1];
		$is_rent = '';
		if(strpos($formCheckParams->type, '_rent')!==false) {
			$type_d = yii::t('rl', 'To_rent_seo');
			$is_rent = yii::t('rl', 'Rents_seo').' ';
			if(in_array($type, array(
				CategoryEnumeration::TRADE_SERVICES_RENT,
				CategoryEnumeration::PUBLIC_CATERING_RENT,
				CategoryEnumeration::LAND_RENT,
				CategoryEnumeration::READY_BUSINESS_RENT,
			))) {
				$type_d = yii::t('rl', 'Rents_seo');
				$is_rent = yii::t('rl', 'To_rent_seo').' ';
			}
		}
		$find_map = ($formCheckParams->list_type=='map')?' '.yii::t('rl', 'Search by map'):'';

		$str_array_top_location = array();
		foreach($array_top_location as $k=>$v) {
			if($objLoc->type!=$v->type && ($v->type==AddressAdvert::TYPE_METRO || $v->type==AddressAdvert::TYPE_METRO_LINE)) {
				unset($array_top_location[$k]);
				continue;
			}
			$str_array_top_location[] = array($v->name, $v->type);
		}
		$count = count($str_array_top_location);
		$preName = '';
		if($count>1) {
			$preName = $str_array_top_location[$count - 2][0];
		}
		$endElem = $str_array_top_location[$count - 1];
		$endName = $endElem[0] . ' ' . $preName;
		if($str_array_top_location[0][0]=='Россия' && $count==2) {
			$endName = $endElem[0];
		}

		$endName .= $find_map;
		$endName = str_replace(array('Россия'), '', $endName);
		if(trim($endName)) {
			$endName = ' — '.$endName;
		}

		//$textT = $type_d.' '.$type_t.' — '.$loc_text;
		$textT = $type_d.' '.$type_t;
		$this->pageTitle = $textT.$endName.' | kvtop.ru';

		$this->pageDescription = yii::t('rl', 'Quick search by ads').' '.$is_rent.$textT.$endName.' | kvtop.ru';
		$text_seo = yii::t('rl', 'Fast reliable high quality').' '.$is_rent.$textT.$endName;
		$text_seo = $is_rent.$textT.$endName;

		$name_view = 'list';
		$zoom_type = false;
		if($formCheckParams->list_type=='map') {
			$name_view = 'list_map';
			$array_types_zoom = array(
				AddressAdvert::TYPE_COUNTRY => 6,
				AddressAdvert::TYPE_ADMINISTRATIVE => 7,
				AddressAdvert::TYPE_SUB_ADMINISTRATIVE => 9,
				AddressAdvert::TYPE_LOCALITY => 10,
				AddressAdvert::TYPE_DEPENDENTLOCALITY => 12,
				AddressAdvert::TYPE_DEPENDENTLOCALITY2 => 14,
				AddressAdvert::TYPE_METRO_LINE => 14,
				AddressAdvert::TYPE_METRO => 14,
				AddressAdvert::TYPE_THOROUGHFARE => 15,
				AddressAdvert::TYPE_PREMISE => 17,
			);
			$zoom_type = $array_types_zoom[end($array_top_location)->type];
		}

		$this->render(DIR_VIEWS_SITE.'realty/objects/'.$name_view, array(
			'var_type'=>$type,
			'var_pages' => $pages,
			'var_elements' => $elements,
			'var_array_sort' =>$array_sort,
			'var_list_clear_params' => $list_clear_params,
			'var_formCheckParams' => $formCheckParams,
			'var_objLoc' => $objLoc,
			'var_array_top_location' => $array_top_location,
			'var_array_types_translit' => $array_types_translit,
			'var_is_sphinx' => $is_sphinx,
			'get_cours_convert' => $get_cours_convert,
			'var_get_type_area_convert' => $get_type_area_convert,
			'var_allDataСurrency' => $allDataСurrency,
			'var_zoom_type' => $zoom_type,
			'var_itemCount' => $itemCount,
			'var_countryName' => $countryObj,
			'var_confTypesFilter' => $confTypesFilter,
			'var_name_total_area' => $name_total_area,
			'var_seo' => $text_seo,
		));
	}

	public function diffDayTop($obj) {
		$d1 = new DateTime($obj->top_date_time);
		$d2 = new DateTime();
		$diff = $d2->diff($d1);
		if($diff->d>=1) {
			return true;
		}
		return $diff->h;
	}
	public function actionSet_top() {
		$this->layout = null;

		$id = Yii::app()->request->getParam('id');
		$obj = Advert::model()->findByPk($id);
		$this->checkMyObj($obj, 'Advert', 'set_top');
		if(!$this->diffDayTop($obj)) {
			$html = '<p class="bg-danger">Объявление уже было поднято</p>';
		}
		else {
			$obj->top_date_time = date('Y-m-d H:i:s');
			$obj->update(array('top_date_time'));
			$html = '<p class="bg-success">Вы удачно подняли объявление</p>';
		}

		yii::app()->end($html);
	}

	public function actionMy($is_a=false, $id_b=false) {
		$user_id = Yii::app()->user->id;
		//form data
		if(isset($_POST['check_elem']) && isset($_POST['type_action']) && count($_POST['check_elem'])<100) {
			$criteria = new CDbCriteria();
			$criteria->addInCondition('id', array_keys($_POST['check_elem']));
			$criteria->addCondition('user_id='.$user_id);
			$criteria->addCondition('status<>'.Advert::STATUS_BLOCK);

			if($_POST['type_action']==2) {
				$status = Advert::STATUS_ARCHIVE;
			}
			else {
				$status = Advert::STATUS_PUBLIC;
			}

			foreach(Advert::model()->findAll($criteria) as $obj) {
				$obj->status = $status;
				$obj->update();
			}

			$this->redirect($this->createUrl(Yii::app()->request->pathInfo, $_GET));
		}
		//

		$criteria = new CDbCriteria();
		$criteria->order= 'id DESC';
		$criteria->addCondition('user_id='.$user_id);
		if($is_a) {
			$criteria->addCondition('status='.Advert::STATUS_ARCHIVE);
		}
		elseif($id_b) {
			$criteria->addCondition('status='.Advert::STATUS_BLOCK);
		}
		else {
			$criteria->addCondition('status='.Advert::STATUS_PUBLIC);
		}

		$itemCount = Advert::model()->count($criteria);

		$pages=new CPagination($itemCount);
		$pages->route = 'objects/my';
		$pages->pageSize=10;
		$pages->applyLimit($criteria);


		$elements = Advert::model()->findAll($criteria);

		$address = $address_all = array();
		foreach($elements as $v) {
			if($v->address_country_id) {
				$address[$v->address_country_id] = 1;
			}
			if($v->address_locality_id) {
				$address[$v->address_locality_id] = 1;
			}
			if($v->address_administrative_id) {
				$address[$v->address_administrative_id] = 1;
			}
			if($v->address_thoroughfare_id) {
				$address[$v->address_thoroughfare_id] = 1;
			}
			if($v->address_premise_id) {
				$address[$v->address_premise_id] = 1;
			}
		}
		if($address) {
			$criteria = new CDbCriteria();
			$criteria->addInCondition('id', array_keys($address));
			$criteria->select = 'name, id';
			$criteria->index = 'id';
			$address_all = AddressAdvert::model()->findAll($criteria);
		}

		$this->render(DIR_VIEWS_SITE.'realty/objects/my_list', array(
			'var_pages' => $pages,
			'var_is_a' => $is_a,
			'var_elements' => $elements,
			'var_address_all' => $address_all,
		));
	}

	public function clearAddress($modelAdvert) {
		$modelAdvert->unsetAttributes(Advert::getAddressParams());
	}
}
