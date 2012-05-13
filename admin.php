<?php

include_once "environment.php";

$page = new Pager('Admin');
$page->title = 'Administration';
$page->pageTitle = 'Page d\'administration du site';

// Traitement des soumissions des formulaires
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
	}
}

$page->addJs('javascript/admin.js');

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

$page->render();

?>