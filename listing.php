<?php

include_once "environment.php";

$page = new Pager('Listing');

if ($VARS->isAjaxRequest()) {
	$idInvite = $VARS->ajax('id', 'int');
	$newStatus = $VARS->ajax('status', 'int');

// 	if (1==Invite::getById($idInvite)->changerStatut($newStatus)) {
// 		echo "{updatedStatus: '$newStatus'}";
// 	}
// 	else {
// 		echo "{state: 'failed to update'}";
// 	}
	$udpatedStatus = Invite::getById($idInvite)->changerStatut($newStatus);
	// echo json_encode(array('updatedStatus' => $udpatedStatus));

	echo $udpatedStatus;
	$page->renderAjax();
}
else {
	$page->title = 'Invités';
	$page->pageTitle = 'Liste des invités';

	$page->addJs('javascript/listing.liste.js');
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
}

?>