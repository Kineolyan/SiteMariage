<?php ?>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title><?php echo $this->title; ?></title>
	<?php foreach ($this->css as $header) { 
		echo "<link rel='stylesheet' href='$header' type='text/css'/>\n";
	} ?>
</head>
<body>
<h1><?php echo $this->pageTitle; ?></h1>

<?php echo $this->connexionForm(); ?>

<h2>Menu :</h2>
<ul>
	<li><a href="index.php">Accueil</a></li>
	<li><a href="infos.php">Informations</a></li>
	<li><a href="listing.php">Listing</a></li>
	<li><a href="facture.php">Facture</a></li>
	<li><a href="admin.php">Admin page</a></li>
</ul>

<div id='content'>
<?php echo $this->content; ?>
</div>

<?php foreach ($this->js as $script) { 
	echo "<script type='text/javascript' src='<?php ?>$script'></script>";
} ?>
</body>
</html>