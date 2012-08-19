<?php

include_once "environment.php";

$page = new Pager('Admin');
$page->headerTitle('Administration');
$page->pageTitle('Page d\'administration du site');

// Traitement des soumissions des formulaires
if ($page->visible()) {
	if ($VARS->isAjaxRequest()) {
		echo Admin::gererAjax();
		$page->renderAjax();
		exit;
	}
	else if (NULL !== $VARS->post('action')) {
		if (Admin::gererSoumission()) {
			Pager::redirect("admin.php");
		}
	}
}

$page->addJs('javascript/admin.js');

echo "<div class='messages'>{$VARS->afficherMessages()}</div>";

?>

<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">Droits d'accès</a></li>
    <li><a href="#tab2" data-toggle="tab">Mots de passe</a></li>
    <li><a href="#tab3" data-toggle="tab">Catégories</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab1">
		<h2>Droits d'accès</h2>

		<?php
		// Créer un formulaire pour chaque utilisateur
		$formUser = new Form('userSelection');
		$htmlForm = $formUser->create('', '', 'selectionForm');

		$users = array();
		$currentUserLogin = 'anonymous';
		if ('' != $VARS->post('editLogin', 'string')) {
			$currentUserLogin = $VARS->post('editLogin', 'string');
		} else if ('' != $VARS->flash('editLogin', 'string')) {
			$currentUserLogin = $VARS->flash('editLogin', 'string');
		}

		$DB->select('users', 'id, login, allowed_pages, admin', '', array('orderBy'=>'login'));
		while ($user = $DB->fetch()) {
			$users[$user['login']] = $user['login'];
			if ($user['login'] == $currentUserLogin) {
				$currentUser = $user;
			}
		}
		$DB->endQuery();
		$htmlForm.= $formUser->select('editLogin', '', $users, $currentUserLogin);
		$htmlForm.= $formUser->submit('editer', 'Go', array('id' => 'goSubmitBtn')).'<br/>';

		$formAccess = new Form('userAccess');
 		$htmlForm.= '<div id="userProfile">'.$formUser->check('admin', 'Est un administrateur', '1', '1'==$currentUser['admin']);

		$htmlForm.= "<ul id='pagesList'>";
		$DB->select('pages', 'id, title', '', array('orderBy'=>'title'));
		$currentSelectedPages = explode(',', $currentUser['allowed_pages']);
		while ($pageItem = $DB->fetch()) {
			$htmlForm.= '<li>'.$formUser->check("selectedPages[]", $pageItem['title'], $pageItem['id'],
					in_array($pageItem['id'], $currentSelectedPages)).'</li>';
		}
		$DB->endQuery();
		$htmlForm.= "</ul>";

		$htmlForm.= $formUser->hidden('action', 'updateAccess');
		$htmlForm.= $formUser->hidden('userId', $currentUser['id']).'</div>';
		$htmlForm.= $formUser->submit('mettreAJour', 'Mettre à jour les droits',
				array('id' => 'accessSubmitBtn', 'class' => 'btn'));
		$htmlForm.= $formUser->end();

		echo $htmlForm;
?>
	</div>
	<div class="tab-pane" id="tab2">

		<h2>Modification de mots de passe</h2>
<?php
		$passwordForm = new Form('passwordForm');
		echo $passwordForm->create('', 'passwordForm', '');
		echo $passwordForm->select('login', 'Login', $users).'</br>';
		echo $passwordForm->password('password1', 'Nouveau mot de passe').'</br>';
		echo $passwordForm->password('password2', 'Confirmation du mot de passe').'</br>';
		echo $passwordForm->hidden('action', 'updatePassword');
		echo $passwordForm->submit('', 'Mettre à jour');
		echo $passwordForm->end();
?>
	</div>
  	<div class="tab-pane" id="tab3">
  		<h2>Gestion de catégories</h2>
  		<div class="row">
  			<div class="span6">
<?php
	if (0 < $DB->select('categories', 'categorie', '', array('orderBy' => 'categorie ASC'))) {
		$liste = '';
		while ($categorie = $DB->fetch()) {
			$liste.= "<li>$categorie[categorie]</li>";
		}
		echo "<ul id='listCategories'>$liste</ul>";
	} else {
		echo '<p id="noCategorie">Pas de catégorie.</p>';
	}
	$DB->endQuery();
?>
  			</div>
  			<div class="span6">
<?php
		$passwordForm = new Form('categories');
		echo $passwordForm->create('', 'categoriesForm', '');
		echo $passwordForm->input('categorie', 'Titre pour la nouvelle catégorie');
		echo $passwordForm->hidden('action', 'ajouterCategorie');
		echo $passwordForm->submit('', 'Ajouter');
		echo $passwordForm->end();
?>
			</div>
		</div>
  	</div>
  </div>
</div>

<?php
$page->render();
?>