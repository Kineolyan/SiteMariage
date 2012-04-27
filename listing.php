<?php

include_once "environment.php";

if ($VISITOR->hasAccess("Listing")) {
	
Pager::generateHeaders('Liste des invités');
?>

<h1>Liste des invités</h1>
<p>En cours de rédaction</p>

<?php 
Pager::generateFooter();

}
else {
	include_once "include/acces.php";
}

?>