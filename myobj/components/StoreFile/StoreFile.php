<?php
class StoreFile extends CComponent {
	const TYPE_IMAGE = 1;
	const TYPE_DOC = 2;
	const TYPE_VIDEO = 3;
	const TYPE_MUSIC = 4;

	const MAX_FILES_FOLDER = 10;

	public static $last_folder;

	public function init() {

	}

	public function getUrl($id, $type) {
		$modelFile = ImageModel::model()->findByPk($id);
		$homedir = 'store_upload';
		return '/'.$homedir.'/'.$modelFile->folder.'/'.$modelFile->code_name.'.jpg';
	}

	public function loadFile(CUploadedFile $file, $type, $arraySetting) {
		$code_name = \MYOBJ\appscms\core\base\SysUtilsString::read_str(10);

		if(static::$last_folder) {
			$folder = static::$last_folder;
		}
		else {
			$last_obj = current(ImageModel::model()->findAll(array('order'=>'id DESC', 'limit'=>1)));
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

		$extension = $file->getExtensionName();

		if($type==static::TYPE_IMAGE) {

			$image = new EasyImage($file->getTempName());

			if(isset($arraySetting['resize'])) {
				$image->resize($arraySetting['resize']['width'], $arraySetting['resize']['height']);
			}

			if(isset($arraySetting['crop'])) {
				$image->crop($arraySetting['crop']['width'], $arraySetting['crop']['height']);
			}

			if(isset($arraySetting['type'])) {
				$extension = $arraySetting['type'];
			}

			$quality = 100;
			if(isset($arraySetting['quality'])) {
				$quality = $arraySetting['quality'];
			}

			$image->save((FOLDER_UPLOAD).$folder.'/'.$code_name.'.'.$extension, $quality);
		}
		else {
			$file->saveAs((FOLDER_UPLOAD).$folder.'/'.$code_name.'.'.$extension, false);
		}

		$objModel = new ImageModel();
		//base
		$objModel->code_name = $code_name;
		$objModel->folder = $folder;
		$objModel->size = $file->getSize();

		if($objModel->validate()) {
			$objModel->save();
		}
		else {
			//кинуть исключение перехватить в контроллере и написать там что системная ошибка отправить в логи админу
		}

		return $objModel;
	}
}
