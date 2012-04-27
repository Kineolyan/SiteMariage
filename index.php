<?php

include_once "environment.php";

if ($VISITOR->hasAccess('Index')) {
	if (isset($_GET['register'])) {
		if ('1'==$_GET['register']) {
			$VISITOR->login('Olivier', 'olivier');
		}
		else {
			$VISITOR->logout();			
		}
	}
	
Pager::generateHeaders("Page d'accueil");
?>

<h1>Bienvenue sur la page d'accueil<?php echo $VISITOR->isLogged()? ', Olivier': ''; ?></h1>
<h2>Menu :</h2>
<ul>
	<li><a href="index.php">Accueil</a></li>
	<li><a href="infos.php">Informations</a></li>
	<li><a href="listing.php">Listing</a></li>
	<li><a href="facture.php">Facture</a></li>
</ul>
<p>
	Login sous "Olivier" par le <a href="index.php?register=1">lien suivant</a><br/>
	Déconnexion avec <a href="index.php?register=0">deconnecter</a>
</p>

<?php 
Pager::generateFooter();

}
else {
	include_once "include/acces.php";
} 

?>