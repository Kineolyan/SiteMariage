<?php
include_once 'util/helpers/pager.class.php';

$this->addCss('css/modal.css');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<?php foreach ($this->css() as $header) {
		echo "<link rel='stylesheet' href='$header' type='text/css'/>\n";
	} ?>
</head>
<body id="modal-<?php echo $this->pageId(); ?>">
	<div class="container">
		<div id='content' class="row">
			<?php echo $this->content(); ?>
		</div>
	</div>

<?php foreach ($this->js() as $script) {
	echo "<script type='text/javascript' src='$script'></script>";
} ?>
</body>
</html>