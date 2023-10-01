<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $sys->getTitle(); ?></title>
</head>

<body>

<div class="leftbar">
	<ul class="menu">
	<?php echo $sys->getMenuHtml();?>
	</ul>
</div>

<div class="content1">
<div class="content"><?php echo $sys->getContent(); ?></div>
</div>

</body>
</html>
