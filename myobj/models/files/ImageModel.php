<?php

class ImageModel extends AbsStoreFilesModel {
	public function tableName() {
		return 'cmsplus_store_file_image';
	}

	public $description;
	public $album_id;
}
