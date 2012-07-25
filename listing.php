<?php

include_once "environment.php";

$page = new Pager('Listing');
$page->headerTitle('Liste des invités');
$page->pageTitle('Liste des invités');

if ($VARS->isAjaxRequest()) {
	$idInvite = $VARS->ajax('id', 'int');
	$newStatus = $VARS->ajax('newStatus', 'string');
	$oldStatus = $VARS->ajax('oldStatus', 'string');
	$udpatedStatus = Invite::getById($idInvite)->changerStatut($newStatus);

	echo '{"updatedStatus":"'.$udpatedStatus.'","oldClass":"'
		.Invite::getStatusClass($oldStatus).'","newClass":"'
		.Invite::getStatusClass($udpatedStatus).'"}';

	$page->renderAjax();
}
else {
	$page->title = 'Invités';
	$page->pageTitle = 'Liste des invités';

	$page->addJs('javascript/listing.liste.js');
	$page->addJs('javascript/jquery.tablesorter.min.js');

// 	$page->sousMenu(array(
// 			'Actions' => array(
// 				'listing.php?view=liste' => 'Liste',
// 				'listing.php?view=registration' => 'Enregistrement'
// 			),
// 			'1' => 'divider',
// 			'Raccourcis' => array(
// 				'listing.php?view=listePerso' => 'Mes inscriptions'
// 			)
// 		));
?>
<div class="span2">
	<ul class="nav nav-list">
		<li class="nav-header">
			Actions
		</li>
		<li><a href='listing.php?view=liste'>Liste</a></li>
		<li><a href='listing.php?view=registration'>Enregistrement</a></li>
		<li class="divider"></li>

		<li class="nav-header">
			Raccourcis
		</li>
		<li><a href='listing.php?view=listePerso'>Mes inscriptions</a></li>
		<?php if('listePerso' == $VARS->get('view')) { ?>
		<li><a href='listing.php'>Liste complète</a></li>
		<?php } ?>
	</ul>
</div>

<div class="span10">
<?php
	$erreur = $VARS->flash("erreur", "string");
	if (NULL != $erreur) {
		echo "<p class=\"alert alert-error\">
			<button class=\"close\" data-dismiss=\"alert\">×</button>
			<strong>Erreur : </strong>$erreur
		</p>";
	}

	$searchBarHtml = '<div class="row" id="searchBar">
			<span id="searchForm" style="display: none">
				<input id="searchItem" value=""/>
				<button id="resetSearch" class="btn">Reset</button>
			</span>
			<span id="loupe" class="btn btn btn-info">Rechercher quelqu\'un</span>
		</div>';

	$liste = new Liste();
	$liste->gererSoumission();
	// Affichage d'une vue
	switch ($VARS->get('view')) {
	case 'registration':
		echo $liste->registrationView();
		$page->addJs('javascript/listing.registration.js');
		break;

	case 'listePerso':
		echo $searchBarHtml . $liste->personnalListView();
		break;

	case 'liste':
	default:
		echo $searchBarHtml . $liste->listView();
	}
?>
</div>
<?php
	$page->render();
}

?>