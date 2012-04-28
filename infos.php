<?php

include_once "environment.php";

$page = new Pager('Infos');
$page->title = 'Informations';
$page->pageTitle = 'Informations pratiques';
$page->content = '<p>Pour trouver des hôtels à proximité et autres</p>';

$page->render();

?>