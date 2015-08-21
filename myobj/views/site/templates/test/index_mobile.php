<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->pageTitle?></title>
</head>
<body>
<div id="header1"><?php echo $this->renderHandle('header1',1)?></div>

<div id="header2"><?php echo $this->renderHandle('header2', 2)?></div>

<div id="header3"><?php echo $content?></div>

<div id="header4"><?php echo $this->renderHandle('header6',6)?></div>

<div id="header4"><?php echo $this->renderHandle('header0',0)?></div>
</body>
</html>