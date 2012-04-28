<?php

include_once "environment.php";

$page = new Pager($VISITOR, 'Facture');

$page->title = 'Facture générale';
$page->pageTitle = 'Facture générale';
$page->content = '<p>En cours de construction</p>';

$page->render();

?>