<?php

include_once "environment.php";

if ($VISITOR->hasAccess("Facture")) {
?>

<html>
<head>
	<title>Page d'accueil</title>
</head>
<body>
	<h1>Bienvenue sur la page d'accueil</h1>
	<p>Loop in the page <a href="#">Accueil</a></p>
</body>
</html>

<?php }
else {
	include_once "include/acces.php";
}

?>