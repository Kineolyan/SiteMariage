<?php

include_once "environment.php";

$page = new Pager('Admin');
$page->title = 'Administration';
$page->pageTitle = 'Page d\'administration du site';

// Traitement des soumissions des formulaires
$messages = '';
if ($page->visible()) {
	switch ($VARS->post('action')) {
		case 'update':
			$id = $VARS->post('userId', 'int');
			$access = $VARS->post('userAccess');
			$adminStatus = $VARS->post('admin');
			$DB->update('users', array(
					'allowed_pages' => $access[0],
					'admin' => $adminStatus[0]
				),
				"id=$id");
			break;

		case 'updatePassword':
			$password1 = $VARS->post('password1', 'string');
			$password2 = $VARS->post('password2', 'string');
			$userId = $VARS->post('login', 'int');
			if ($password1 == $password2 && 0 != $userId) {
				$DB->update('users',
					array('password' => Visitor::cryptPassword($password1)),
					"id=$userId");
				$messages.= "<p>Nouveau mot de passe pour $userId: $password1 [ok]</p>";
			}
			else {
			}
			break;
	}
}

$page->addJs('javascript/admin.js');

if (''!=$messages) {
	echo "<div id='messages'>$messages</div>";
}

?>

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
	$htmlForm.= $form->check('admin', 'admin',
			array('1'=> '1'==$user['admin']));

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

$page->render();

?>