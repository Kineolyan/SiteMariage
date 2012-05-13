<?php 
include_once 'util/helpers/pager.class.php';
?>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title><?php echo $this->title(); ?></title>
	<?php foreach ($this->css() as $header) { 
		echo "<link rel='stylesheet' href='$header' type='text/css'/>\n";
	} ?>
</head>
<body>
<h1><?php echo $this->pageTitle(); ?></h1>

<?php echo $this->connexionForm(); ?>

<?php echo $this->getNavigation(); ?>

<div id='content'>
<?php echo $this->content(); ?>
</div>

<?php foreach ($this->js() as $script) { 
	echo "<script type='text/javascript' src='$script'></script>";
} ?>
</body>
</html>