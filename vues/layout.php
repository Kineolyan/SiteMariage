<?php
include_once 'util/helpers/pager.class.php';
?>
<html lang="fr">
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title><?php echo $this->headerTitle(); ?></title>
	<?php foreach ($this->css() as $header) {
		echo "<link rel='stylesheet' href='$header' type='text/css'/>\n";
	} ?>
</head>
<body>
	<div class="container">

	<div class="row">
		<div id="<?php echo ($this->m_visitor->isLogged())? 'connected': 'connection' ?>Container"
			class="span3 offset9">
			<?php echo $this->connexionForm(); ?>
		</div>
	</div>

	<div class="navbar">
	  <div class="navbar-inner">
	    <div class="container">
	<?php echo $this->getNavigation(); ?>
	</div>
	  </div>
	</div>

	<h1>
		<div class="row">
			<?php echo $this->pageTitle(); ?>
		</div>
	</h1>
	<div id='content' class="row">
		<?php echo $this->content(); ?>
	</div>

	<?php foreach ($this->js() as $script) {
		echo "<script type='text/javascript' src='$script'></script>";
	} ?>
	</div>
</body>
</html>