<?php

include_once "environment.php";
	
$page = new Pager($VISITOR, 'Index');
$page->title = 'Accueil';
$page->pageTitle = 'Bienvenue sur la page d\'accueil'.($VISITOR->isLogged()? ', Olivier': '');
$page->content = '<p>Bonjour tout le monde</p>';

$page->render();

?>