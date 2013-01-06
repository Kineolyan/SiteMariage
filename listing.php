<?php

include_once "environment.php";

$page = new Pager('Listing');
$page->headerTitle('Liste des invités');
$page->pageTitle('Liste des invités');
$page->addCss('css/liste.css');

if ($VARS->isAjaxRequest()) {
	switch ($VARS->ajax('action')) {
		case 'updateStatus':
			$idInvite = $VARS->ajax('id', 'int');
			$newStatus = $VARS->ajax('newStatus', 'string');
			$oldStatus = $VARS->ajax('oldStatus', 'string');
			$udpatedStatus = Invite::getById($idInvite)->changerStatut($newStatus);

			echo '{"updatedStatus":"'.$udpatedStatus.'","oldClass":"'
				.Invite::getStatusClass($oldStatus).'","newClass":"'
				.Invite::getStatusClass($udpatedStatus).'"}';
			break;

		case 'getCategories':
			echo implode('|', Categories::getCategories());
		
		default:
			// do nothing
			break;
	}

	$page->renderAjax();
}
else {
	$page->title = 'Invités';
	$page->pageTitle = 'Liste des invités';

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
		<li><a href='listing.php?view=categories'>Catégories</a></li>
		<li><a href='listing.php?view=faire-part'>Faire-parts</a></li>
		<li class="divider"></li>

		<li class="nav-header">
			Raccourcis
		</li>
		<?php if('listePerso' == $VARS->get('view')) { ?>
		<li><a href='listing.php'>Liste complète</a></li>
		<?php } else { ?>
		<li><a href='listing.php?view=listePerso'>Mes inscriptions</a></li>
		<?php } ?>
	</ul>
</div>

<div class="span10">
<?php
	echo "<div class='row'>{$VARS->afficherMessages()}</div>";

	$searchBarHtml = '<div id="searchBar"></div>';
	$selectBarHtml = '<div id="selectBar"></div>';

	$liste = new Liste();
	$liste->gererSoumission();
	// Affichage d'une vue
	switch ($VARS->get('view')) {
	case 'registration':
		echo $liste->registrationView();
		$page->addJs('javascript/listing.registration.js');
		break;

	case 'listePerso':
		echo "<div class='row'>$selectBarHtml $searchBarHtml</div>" . $liste->personnalListView();
		$page->addJs('javascript/listing.liste.js');
		$page->addJs('javascript/jquery.tablesorter.min.js');
		break;

	case 'edition':
		echo $liste->editionView();
		break;

	case 'categories':
		echo "<div class='row'>$searchBarHtml</div>" . $liste->categoriesView();
		$page->addJs('javascript/listing.categories.js');
		break;

	case 'faire-part':
		echo "<div class='row'>$searchBarHtml</div>" . $liste->sendingView();
		$page->addJs('javascript/listing.sending.js');
		break;

	case 'liste':
	default:
		echo "<div class='row'>$selectBarHtml $searchBarHtml</div>" . $liste->listView();
		$page->addJs('javascript/listing.liste.js');
		$page->addJs('javascript/jquery.tablesorter.min.js');
	}
?>
</div>
<?php
	$page->render();
}

?>