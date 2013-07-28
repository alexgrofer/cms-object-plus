<?php
Yii::app()->getRequest()->sendFile($namefile, $content, $typefile, $terminate);