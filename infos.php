<?php

include_once "environment.php";

$page = new Pager('Infos');
$page->headerTitle('Informations');
$page->pageTitle('Informations pratiques');
?>

<p>Pour trouver des hôtels à proximité et autres</p>

<?php
$page->render();
?>