<?php
class CmsRelatedBehavior extends CActiveRecordBehavior
{
	public function links_edit($type, $namerelation, array $idsObj=array(), $addparam=null) {
		$thisObj = $this->getOwner();
		$thisTable = $thisObj->tableName();
		$thisObjNamePrimaryKey = $thisObj->primaryKey();
		$thisObjPrimaryKeyVal = $thisObj->primaryKey;
		$thisRelations = $thisObj->relations();
		$thisRelation = $thisRelations[$namerelation];
		$typeThisRelation = $thisRelation[0];
		$nameModelThisRelation = $thisRelation[1];
		$nameLinkPrimaryKeyThisRelation = $thisRelation[2];
		$objModelThisRelation = $nameModelThisRelation::model();
		$nameTableThisRelation = $objModelThisRelation->tableName();
		$namePrimaryKeyThisRelation = $objModelThisRelation->primaryKey();

		if($typeThisRelation==CActiveRecord::MANY_MANY) {
			$arrDataM = preg_split('/[,()]/', $thisRelation[2]);
			$mtmNameTable = trim($arrDataM[0]);
			$mtmFromPrimaryKey = trim($arrDataM[1]);
			$mtmToPrimaryKey = trim($arrDataM[2]);
		}

		$command = Yii::app()->db->createCommand();
		$transaction=Yii::app()->db->beginTransaction();
		try {
		switch($type) {
			case 'add':
				if($typeThisRelation == CActiveRecord::MANY_MANY) {
					foreach($idsObj as $id) {
						$command->insert($mtmNameTable, array_merge(array($mtmFromPrimaryKey=>$thisObjPrimaryKeyVal, $mtmToPrimaryKey=>$id),((is_array($addparam))?$addparam:array())));
					}
				}
				elseif(in_array($typeThisRelation, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY))) {
					$command->update($nameTableThisRelation, array($nameLinkPrimaryKeyThisRelation => $thisObjPrimaryKeyVal), array('in', $namePrimaryKeyThisRelation, $idsObj));
				}
				elseif($typeThisRelation == CActiveRecord::BELONGS_TO) {
					$command->update($thisTable, array($nameLinkPrimaryKeyThisRelation => $idsObj[0]), $namePrimaryKeyThisRelation.'='.$thisObjPrimaryKeyVal);
				}
			break;
			case 'edit':
				foreach($idsObj as $id) {
					$command->update($mtmNameTable, $addparam, array('and', $mtmFromPrimaryKey.'='.$thisObjPrimaryKeyVal, $mtmToPrimaryKey.'='.$id));
				}
			break;
			case 'select':
				if(!$thisObj->isNewRecord) {
					return $command->select($addparam)->from($mtmNameTable)->where(array('and', $mtmFromPrimaryKey.'='.$thisObjPrimaryKeyVal, $mtmToPrimaryKey.'='.$idsObj))->queryRow();
				}
				return false;
			break;
			case 'remove':
				if($typeThisRelation == CActiveRecord::MANY_MANY) {
					$command->delete($mtmNameTable,array('and', $mtmFromPrimaryKey.'='.$thisObjPrimaryKeyVal, array('in', $mtmToPrimaryKey, $idsObj)));
				}
				elseif(in_array($typeThisRelation, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY))) {
					$command->update($nameTableThisRelation, array($nameLinkPrimaryKeyThisRelation => null), array('in', $namePrimaryKeyThisRelation, $idsObj));
				}
				elseif($typeThisRelation == CActiveRecord::BELONGS_TO) {
					$command->update($thisTable, array($nameLinkPrimaryKeyThisRelation => null), $namePrimaryKeyThisRelation.'='.$thisObjPrimaryKeyVal);
				}
			break;
			case 'clear':
				if($typeThisRelation == CActiveRecord::MANY_MANY) {
					$command->delete($mtmNameTable,$mtmFromPrimaryKey.'='.$thisObjNamePrimaryKey);
				}
			break;

		}
			$transaction->commit();
				}
		catch(Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	return true;
	}
}
