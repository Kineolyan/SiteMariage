<?php

include_once "environment.php";

$page = new Pager('Admin');
$page->title = 'Administration';
$page->pageTitle = 'Page d\'administration du site';

// Traitement des soumissions des formulaires
if ($page->visible() && $VARS->hasVars('post')) {
	$DB->update('users', array(
			'allowed_pages' => $VARS->post('userAccess'),
			'admin' => ($VARS->post('admin', 'int'))
		),
		"id={$VARS->post('userId')}");
}

$page->addJs('javascript/admin.js');

// Liste des pages et composants
$listPages = '';
$DB->select('pages', 'id, title', '', array('orderBy'=>'id'));
while ($pageItem = $DB->fetch()) {
	$listPages.= "<li>[{$pageItem['id']}] - $pageItem[title]";
}
$DB->endQuery();

$usersForms = '';
$DB->select('users', '*', '', array('orderBy'=>'login'));
while ($user = $DB->fetch()) {
	$form = new Form('userAccess');
	$htmlForm = $form->create('', '', 'accessForm');
	$htmlForm.= $form->hidden('userId', $user['id']);
	$htmlForm.= $form->input('userAccess', $user['login'], $user['allowed_pages']).'</br>';
	$htmlForm.= $form->check('admin', 'admin', 
			array('1'=> '1'==$user['admin']));
	$htmlForm.= $form->submit('update', 'Mettre à jour');
	$htmlForm.= $form->end();
	
	$usersForms.= $htmlForm;
}
$DB->endQuery();

$page->content = "<h2>Pages du site</h2>
<ul id='pageList'>$listPages</ul>
$usersForms";

$page->render();

?>