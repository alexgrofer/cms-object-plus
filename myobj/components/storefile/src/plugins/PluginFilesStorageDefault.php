<?php
/*
 * планиг отвечает за всю работу c данными по сути любой сущьности, т.е вся логика создание файла на сервере или вне его
 * в интерфейсе должны быть заданы методы которые могут быть вызванны из компонента CStoreFiles
 */

/*
 * погружаем класс для работы со списками файлов - свойственнен только для этого плагина
 */
Yii::import('application.modules.myobj.models.modul_storefile.filesStorage');
class PluginFilesStorageDefault implements InterfaceFilesStorage
{
	CONST URL_HOME = null;
	//Обязательные методы
	public static function newobj($params) {}
	public static function get($params) {}
	public static function editobj($params) {}
	public static function delobj($params) {}
	//Остальные пользовательские методы
	public static function setRules(filesStorage $model) {
		/* $files = CUploadedFile::getInstancesByName('EmptyForm[file]');
		if(count($files)==0) {
			$model->_rules[] = array('url','required');
		} */
		foreach($model->_rules as $key => $elem) {
			if($elem[0]=='file') {
				$model->_rules[$key] = array('file', 'file', 'maxFiles'=>10, 'maxSize'=>((1024*1024)*16), 'allowEmpty'=>true, 'safe'=>true); //'types' => 'zip, rar',
				break;
			}
		}
		return $model->_rules;
	}
	protected static function randName() {
		$varStr = 'qwertyuiopasdfghjklzxcvbnm1234567890';
		return substr(str_shuffle($varStr.$varStr),-22);
	}
	public static function deleteFiles($url_files) {
		$dirhome = Yii::getPathOfAlias('webroot.'.Yii::app()->appcms->config['homeDirStoreFile']).DIRECTORY_SEPARATOR;
		$files = json_decode($url_files);
		foreach($files as $urlelem) {
			$FilesPath=$dirhome.$urlelem;
			if(is_file($FilesPath)) unlink($FilesPath);
		}
	}
	public static function befdel($url_files) {
		static::deleteFiles($url_files);
	}
	public function editelems($arr_ElementsForm,filesStorage $model) {
		if(count(json_decode($model->url))>1) $arr_ElementsForm['is_addFile']['checked'] = 'checked';
		return $arr_ElementsForm;
	}
	public static function procFile(filesStorage $model) {
		/* var @file CUploadedFile*/
		$files = CUploadedFile::getInstancesByName('EmptyForm[file]'); //or not Multiple getInstanceByName

		$dirhome = (static::URL_HOME)? static::URL_HOME : Yii::getPathOfAlias('webroot.'.Yii::app()->appcms->config['homeDirStoreFile']);
		$dirhome .= ((substr($dirhome,-1)==DIRECTORY_SEPARATOR)?'':DIRECTORY_SEPARATOR);
		$model->user_folder = trim($model->user_folder);
		$url_dir = '';
		if($model->user_folder) {
			$url_dir = $model->user_folder.((substr($model->user_folder,-1)=='/')?'':'/');
			$dirhome .= $model->user_folder.((substr($model->user_folder,-1)==DIRECTORY_SEPARATOR)?'':DIRECTORY_SEPARATOR);
		}

		$decode_url = ($model->url)?json_decode($model->url):array();
		//rename or delete obj files
		if(!$model->isNewRecord && !count($files) && $_POST['EmptyForm']['url']!=$model->old_attributes['url']) {
			$url = $_POST['EmptyForm']['url'];
			$decode_url_old = json_decode($model->old_attributes['url']);
			$i=0;
			$isdel=0;
			foreach($decode_url as $urlfile) {
				$newname = $urlfile;
				if($model->is_randName) {
					$newname = static::randName().(substr(strrchr($newname,'.'),0));
				}

				if(trim($decode_url[$i])=='') {
					$isdel=1;
					unset($decode_url[$i]);
				}
				elseif($decode_url_old[$i]!=$decode_url[$i] && !in_array($decode_url[$i], $decode_url_old)) {
					rename($dirhome.$decode_url_old[$i], $dirhome.$newname);
				}
				elseif(in_array($decode_url[$i], $decode_url_old)) {
					//add names если бедет массив имен перемешать нужным образом при перемешивании urls
				}
				$i++;
			}

			if($isdel) {
				$array_diff = array_diff($decode_url_old,$decode_url);
				static::deleteFiles(json_encode($array_diff));
				$decode_url = array_values($decode_url);
			}
		}
		//add files
		elseif(count($files)) {
			if(!$model->isNewRecord && !$_POST['EmptyForm']['is_addFile'] && $model->url)	{
				static::deleteFiles($model->url);
				$decode_url = array();
			}

			foreach($files as $file) {
				$name = $file->name;
				if($model->is_randName) {
					$name = static::randName().(substr(strrchr($file->name,'.'),0));
				}
				$decode_url[] = $url_dir.$name;
				if(!$_POST['EmptyForm']['force_save']) {
					if(file_exists($dirhome.$name)) {
						$model->addError('file', 'file exists '.$name);
						throw new CException(Yii::t('cms',serialize($model->getErrors()))); //task normal error form
					}
				}
				$file->saveAs($dirhome.$name);
			}
		}

		$model->url = ($decode_url)?json_encode($decode_url):'';
		$model->updateByPk($model->id,$model->attributes);
		return true;
	}
}