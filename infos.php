<?php

include_once "environment.php";

if ($VISITOR->hasAccess("Infos")) {
	
Pager::generateHeaders('Informations');
?>

<h1>Bienvenue sur la page d'accueil</h1>
<p>Loop in the page <a href="#">Accueil</a></p>

<?php 
Pager::generateFooter();

}
else {
	include_once "include/acces.php";
}

?>