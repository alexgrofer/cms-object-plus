<?php
class StoreFile extends CComponent {
	const TYPE_IMAGE = 1;
	const TYPE_DOC = 2;
	const TYPE_VIDEO = 3;
	const TYPE_MUSIC = 4;

	const MAX_FILES_FOLDER = 255;

	public static $last_folder;

	public function init() {

	}

	public function getUrl($id, $type) {
		$modelFile = ImageModel::model()->findByPk($id);
		$homedir = 'store_upload';
		return '/'.$homedir.'/'.$modelFile->folder.'/'.$modelFile->code_name;
	}

	protected function getUrlObjServer($model) {
		return (FOLDER_UPLOAD).$model->folder.'/'.$model->code_name;
	}

	public function loadFile($id_or_path, $type, $arraySetting) {
		$extension = '';
		$quality = 100;

		// name model
		if($type==static::TYPE_IMAGE) {
			$modelFile = 'ImageModel';
		}
		//

		// create folder
		if(static::$last_folder) {
			$folder = static::$last_folder;
		}
		else {
			$last_obj = current($modelFile::model()->findAll(array('order'=>'id DESC', 'limit'=>1)));
			if($last_obj) {
				$folder = $last_obj->folder;
				$absFolder = (FOLDER_UPLOAD) . $folder;
			}

			if(!$last_obj || file_exists($absFolder)==false || (count(scandir($absFolder)) - 2)>=static::MAX_FILES_FOLDER) {
				$folder = \MYOBJ\appscms\core\base\SysUtilsString::read_str(25);
				static::$last_folder = $folder;
				CFileHelper::createDirectory((FOLDER_UPLOAD).$folder);
			}
		}
		//

		if($type==static::TYPE_IMAGE) {
			if((int)$id_or_path>0) {
				$objModel = $modelFile::model()->findByPk($id_or_path);
				$code_name = $objModel->code_name;
				$image = new EasyImage($this->getUrlObjServer($objModel));
			}
			else {
				$objModel = new $modelFile();
				$code_name = \MYOBJ\appscms\core\base\SysUtilsString::read_str(10);
				$image = new EasyImage($id_or_path);
			}

			if(isset($arraySetting['resize'])) {
				$image->resize($arraySetting['resize']['width'], $arraySetting['resize']['height']);
			}

			if(isset($arraySetting['crop'])) {
				$image->crop($arraySetting['crop']['width'], $arraySetting['crop']['height']);
			}

			if(isset($arraySetting['type'])) {
				$extension = '.'.$arraySetting['type'];
			}

			if(isset($arraySetting['quality'])) {
				$quality = $arraySetting['quality'];
			}

			if(isset($arraySetting['rotate'])) {
				$image->rotate($arraySetting['rotate']);
			}

			$image->save((FOLDER_UPLOAD).$folder.'/'.$code_name.$extension, $quality);
		}
		else {
			if(is_uploaded_file($this->_tempName))
				return copy($id_or_path, (FOLDER_UPLOAD).$folder.'/'.$code_name.$extension);
			else
				return false;
		}

		//save model
		$objModel->code_name = $code_name.$extension;
		$objModel->folder = $folder;
		$objModel->size = '';
		//

		if($objModel->validate()) {
			$objModel->save();
		}
		else {
			//кинуть исключение перехватить в контроллере и написать там что системная ошибка отправить в логи админу
		}

		return $objModel;
	}
}
