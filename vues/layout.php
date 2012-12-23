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

		<div class="navbar">
		  <div class="navbar-inner">
		    <div class="container">
					<?php echo $this->getNavigation(); ?>
				</div>
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

		<?php foreach ($this->js() as $script) {
			echo "<script type='text/javascript' src='$script'></script>";
		} ?>

	</div>
</body>
</html>