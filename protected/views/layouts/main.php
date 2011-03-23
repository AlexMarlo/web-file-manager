<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="en" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/flexigrid/flexigrid.css">
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/lib/jquery/jquery.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/lib/flexigrid.js"></script>

<?php if(!Yii::app()->user->isGuest): ?>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/lib/jquery.MultiFile.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/lib/jquery.form.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/lib/jquery.blockUI.js"></script>
<?php endif; ?>

<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<div id="page">

<div id="header">
<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
<div id="mainmenu">
<?php $this->widget('application.components.MainMenu',array(
	'items'=>array(
		array('label'=>'File Manager',   'url'=>array('/file/manager')),
		array('label'=>'Users',  'url'=>array('/user'), 'visible'=>Yii::app()->user->name=='admin'),
		array('label'=>'Options',  'url'=>array('/options'), 'visible'=>Yii::app()->user->name=='admin'),
		array('label'=>'Contact','url'=>array('/site/contact')),
		array('label'=>'Login',  'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
		array('label'=>'Logout', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest))
		)); ?>
</div><!-- mainmenu -->
</div><!-- header -->

<div id="content">
<?php echo $content; ?>
</div><!-- content -->

<div id="footer">
Copyright &copy; 2009 by My Company.<br/>
All Rights Reserved.<br/>
<?php echo Yii::powered(); ?>
</div><!-- footer -->

</div><!-- page -->
</body>

</html>








