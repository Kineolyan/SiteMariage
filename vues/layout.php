<?php
include_once 'util/helpers/pager.class.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title><?php echo $this->headerTitle(); ?></title>
	<?php foreach ($this->css() as $header) {
		echo "<link rel='stylesheet' href='$header' type='text/css'/>\n";
	} ?>
</head>
<body id="<?php echo $this->pageId(); ?>">
	<div class="container">

		<div class="row">
			<div id="<?php echo ($this->m_visitor->isLogged())? 'connected': 'connection' ?>Container" class="span3 offset9">
				<?php echo $this->connexionForm(); ?>
			</div>
		</div>

		<div class="navbar navbar-inverse">
		 	<div class="navbar-inner">
				<?php echo $this->getNavigation(); ?>
			</div>
		</div>

		<?php if ('' != $this->pageTitle()) { ?>
		<div class="row">
			<h1><?php echo $this->pageTitle(); ?></h1>
		</div>
		<?php } ?>
		
		<div id='content' class="row">
			<?php echo $this->content(); ?>
		</div>
	</div>

<?php foreach ($this->js() as $script) {
	echo "<script type='text/javascript' src='$script'></script>";
} ?>

<?php include_once "scripts/analytics.php"; ?>
</body>
</html>