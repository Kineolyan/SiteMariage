<?php

include_once "environment.php";
	
$page = new Pager('Index');
$page->title = 'Accueil';
$page->pageTitle = 'Bienvenue sur la page d\'accueil'.($VISITOR->isLogged()? ', Olivier': '');
?>

<p>Bonjour tout le monde</p>

<?php
$page->render();
?>