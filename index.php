<?php

include_once "environment.php";

$page = new Pager('Index');
$page->headerTitle('Bienvenue sur le site de Colombe et Olivier');
$page->addCss("css/index.css");
?>
<div class="row" id='titre'>
	<img id="faire_part" src="data/Cocoliver.png"/>
<div class="row">
	<img id="faire_part" src="data/faire-part.png"/>
</div>
<div class="row">
	<a id="bouton" class="btn" href="accueil.php">Start</a>
</div>
<?php
$page->render('layout_entree.php');
?>