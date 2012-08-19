<?php

include_once "environment.php";

$page = new Pager('Admin');
$page->headerTitle('Administration');
$page->pageTitle('Page d\'administration du site');

// Traitement des soumissions des formulaires
if ($page->visible()) {
	if ($VARS->isAjaxRequest()) {
		Admin::gererAjax();
		$page->renderAjax();
	}
	else if (NULL !== $VARS->post('action')) {
		Admin::gererSoumission();
		Pager::redirect("admin.php");
	}
}

$page->addJs('javascript/admin.js');

$messages = $VARS->get('message');
if (NULL !== $messages) {
	echo "<div class='alert alert-success'>$messages</div>";
}

$page->afficherErreurs();

?>

<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">Droits d'accès</a></li>
    <li><a href="#tab2" data-toggle="tab">Mots de passe</a></li>
    <li><a href="#tab3" data-toggle="tab">Catégories</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab1">
		<h2>Pages du site</h2>
		<ul id='pageList'>
		<?php
		$DB->select('pages', 'id, title', '', array('orderBy'=>'id'));
		while ($pageItem = $DB->fetch()) {
			echo "<li>[{$pageItem['id']}] - $pageItem[title]";
		}
		$DB->endQuery();
		?>
		</ul>

		<?php

		// Créer un formulaire pour chaque utilisateur
		$usersForms = '';
		$DB->select('users', '*', '', array('orderBy'=>'login'));
		while ($user = $DB->fetch()) {
			$form = new Form('userAccess', $user['id']);
			$htmlForm = $form->create('', '', 'accessForm');
			$htmlForm.= $form->input('userAccess', $user['login'], $user['allowed_pages']).'</br>';
			$htmlForm.= $form->check('admin', 'admin', '1', '1'==$user['admin']).'<br/>';

			$form->useId(false);
			$htmlForm.= $form->hidden('action', 'update');
			$htmlForm.= $form->hidden('userId', $user['id']);
			$htmlForm.= $form->submit('', 'Mettre à jour');
			$htmlForm.= $form->end();

			$usersForms.= $htmlForm;
		}
		$DB->endQuery();

		echo $usersForms;

		// Créer un formulaire pour changer le mot de passe
		$DB->select('users', 'id, login', '', array('orderBy'=>'login'));
		$users = array();
		while ($user = $DB->fetch()) {
			$users[$user['id']] = $user['login'];
		}
		$DB->endQuery();
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
  			<div class="span6 offset6">
<?php
		$passwordForm = new Form('categories');
		echo $passwordForm->create('', 'categories', '');
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