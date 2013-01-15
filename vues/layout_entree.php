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
<body>
	<div class="container">
		<?php echo $this->content(); ?>
	</div>

<?php include_once "scripts/analytics.php"; ?>
</body>
</html>