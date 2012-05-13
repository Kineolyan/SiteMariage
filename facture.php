<?php

include_once "environment.php";

$page = new Pager('Facture');

$page->title = 'Facture générale';
$page->pageTitle = 'Facture générale';
?>

<p>En cours de construction</p>

<?php 
$page->render();
?>