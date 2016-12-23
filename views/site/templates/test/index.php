<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$this->pageTitle?></title>
</head>
<body>
<div id="header1"><?=$this->renderHandle('header1',1)?></div>

<div id="header2"><?=$this->renderHandle('header2', 2)?></div>

<div id="header3"><?=$content?></div>

<div id="header4"><?=$this->renderHandle('header6',6)?></div>

<div id="header4"><?=$this->renderHandle('header0',0)?></div>
</body>
</html>