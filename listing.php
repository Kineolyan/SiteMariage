<?php

include_once "environment.php";

$page = new Pager($VISITOR, 'Listing');
$page->title = 'Invités';
$page->pageTitle = 'Liste des invités';
$page->content = '<p>En cours de rédaction</p>';

$page->render();

?>