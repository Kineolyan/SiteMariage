<?php

include_once "environment.php";

$page = new Pager('Listing');
$page->title = 'Invités';
$page->pageTitle = 'Liste des invités';

?>
<h3>Sous-menu</h3>
<p>
	<a href='listing.php?view=liste'>Liste</a>&nbsp;
	<a href='listing.php?view=registration'>Enregistrement</a>
</p>
<?php 

$liste = new Liste();
$liste->gererSoumission();
// Affichage d'une vue
switch ($VARS->get('view')) {
	case 'registration':
		echo $liste->registrationView();
		$page->addJs('javascript/listing.registration.js');
		break;
		
	case 'liste':
	default:
		echo $liste->listView();
}

$page->render();

?>