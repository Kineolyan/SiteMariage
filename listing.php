<?php

include_once "environment.php";

$page = new Pager('Listing');
$page->title = 'Invités';
$page->pageTitle = 'Liste des invités';


$liste = new Liste();
$page->content = $liste->listView();

$page->render();

?>