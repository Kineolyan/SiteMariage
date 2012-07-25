<?php

include_once "environment.php";

$page = new Pager('Index');
$page->headerTitle('Bienvenue sur le site de Colombe et Olivier');
$page->addCss("css/index.css");
?>
<p>
	<img id="faire_part" src="data/faire-part.png"/><br/>
	<!--  <a href="accueil.php"><img id="bouton" src="data/bouton.png"/></a> -->
	<a id="bouton" class="btn" href="accueil.php">Démarrer l'aventure</a>
</p>
<?php
$page->render('layout_entree.php');
?>