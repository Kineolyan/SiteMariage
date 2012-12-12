<?php

include_once "environment.php";

$page = new Pager('Index');
$page->headerTitle('Accueil');
$page->pageTitle('Bonjour et bienvenue sur le site du mariage de Colombe & Olivier');
?>

<p>Sur ce petit site, vous trouverez surtout des informations sur la salle, comment y aller, les bonnes adresses dans les alentours 
	où vous pourrez dormir, ...</p>

<?php
$page->render();
?>