<?php
class CmsRelatedBehavior extends CActiveRecordBehavior
{
	public function links_edit($type, $namerelation, array $idsObj=null, $addparam=array(), $where=array()) {
		$thisObj = $this->getOwner();
		$thisTable = $thisObj->tableName();
		$thisObjPrimaryKeyVal = $thisObj->primaryKey;
		$thisRelations = $thisObj->metaData->relations;
		$thisRelation = $thisRelations[$namerelation];
		$typeThisRelation = get_class($thisRelation);
		$nameModelThisRelation = $thisRelation->className;
		$nameLinkPrimaryKeyThisRelation = $thisRelation->foreignKey;
		$modelThisRelation = new $nameModelThisRelation();
		$nameTableThisRelation = $modelThisRelation->tableName();
		$namePrimaryKeyThisRelation = $modelThisRelation->primaryKey();

		if($typeThisRelation==CActiveRecord::MANY_MANY) {
			$arrDataM = preg_split('/[,()]/', $nameLinkPrimaryKeyThisRelation);
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
						$command->insert($mtmNameTable, array_merge(array($mtmFromPrimaryKey=>$thisObjPrimaryKeyVal, $mtmToPrimaryKey=>$id), $addparam));
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
				if($typeThisRelation == CActiveRecord::MANY_MANY) {
					$command->update($mtmNameTable, $addparam, array('and', $mtmFromPrimaryKey.'='.$thisObjPrimaryKeyVal, array('in', $mtmToPrimaryKey, $idsObj)));
				}
				/* ??? доделать
				elseif(in_array($typeThisRelation, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY))) {
					$command->update($nameTableThisRelation, array($nameLinkPrimaryKeyThisRelation => null), array('in', $namePrimaryKeyThisRelation, $idsObj));
				}
				elseif($typeThisRelation == CActiveRecord::BELONGS_TO) {
					$command->update($thisTable, array($nameLinkPrimaryKeyThisRelation => null), $namePrimaryKeyThisRelation.'='.$thisObjPrimaryKeyVal);
				}
				*/
			break;
			case 'remove':
				if($typeThisRelation == CActiveRecord::MANY_MANY) {
					$command->delete($mtmNameTable,array('and', $mtmFromPrimaryKey.'='.$thisObjPrimaryKeyVal, array('in', $mtmToPrimaryKey, $idsObj), $where));
				}
				elseif(in_array($typeThisRelation, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY))) {
					$command->delete($nameTableThisRelation, array('in', $namePrimaryKeyThisRelation, $idsObj));
				}
				elseif($typeThisRelation == CActiveRecord::BELONGS_TO) {
					$command->update($thisTable, array($nameLinkPrimaryKeyThisRelation=>null), array('and', $thisObj->primaryKey().'='.$thisObjPrimaryKeyVal));
					//$command->delete($thisTable, $namePrimaryKeyThisRelation.'='.$thisObjPrimaryKeyVal);
				}
			break;
			case 'clear':
				if($typeThisRelation == CActiveRecord::MANY_MANY) {
					$command->delete($mtmNameTable,array('and', $mtmFromPrimaryKey.'='.$thisObjPrimaryKeyVal, $where));
				}
				elseif($typeThisRelation == CActiveRecord::HAS_MANY) {
					$command->delete($nameTableThisRelation, $nameLinkPrimaryKeyThisRelation.'='.$thisObjPrimaryKeyVal);
				}
			break;

		}
		$transaction->commit();

		}
		catch(Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
		if($type=='select') {
			if(!$thisObj->isNewRecord) {
				return $command->select($addparam)->from($mtmNameTable)->where(array('and', $mtmFromPrimaryKey.'='.$thisObjPrimaryKeyVal, $mtmToPrimaryKey.'='.$idsObj[0]))->queryRow();
			} return null;
		}
	}
}
