<?php

use Util\Helpers\Pager;
use Util\Helpers\Form;
	
$page = new Pager('RegistrationForm');

$form = new Form('registration');
$participant = $form->input('nom', 'Nom');
$participant.= $form->input('prenom', 'Prenom');

$page->content = <<<REGISTRATION
<form id='registrationForm' action=''>
	<div class='participant'>
		$participant
	</div>
</form>
REGISTRATION;

$page->renderComponent();

?>