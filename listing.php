<?php

include_once "environment.php";

$page = new Pager('Listing');
$page->title = 'Invités';
$page->pageTitle = 'Liste des invités';

$page->content = <<<SOUS_MENU
<h3>Sous-menu</h3>
<p>
	<a href='listing.php?view=liste'>Liste</a>&nbsp;
	<a href='listing.php?view=registration'>Enregistrement</a>
</p>
SOUS_MENU;

$liste = new Liste();
$liste->gererSoumission();
// Affichage d'une vue
switch ($VARS->get('view')) {
	case 'registration':
		$page->content.= $liste->registrationView();
		break;
		
	case 'liste':
	default:
		$page->content.= $liste->listView();
}

$page->render();

?>