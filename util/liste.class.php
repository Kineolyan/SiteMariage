<?php

require_once 'invite.class.php';

class Liste {
	private $m_db;
	private $m_visitor;

	public function __construct() {
		global $DB, $VISITOR;

		$this->m_db = $DB;
		$this->m_visitor = $VISITOR;
	}

	// Méthodes de gestion des invités

	public function gererSoumission() {
		global $VARS;

		switch ($VARS->post('action')) {
		case 'registration':
			$this->registerGuests();
			Pager::redirect('listing.php?view=liste');
			break;
		default:
			break;
		}
	}

	public function registerGuests() {
		global $VARS;

		$noms = $VARS->post('nom');
		$prenoms = $VARS->post('prenom');
		$nbInvites = count($noms);

		// Récupérer l'id du responsable
		$responsableId = $VARS->post('responsable', 'int');
		if (NULL == $responsableId || 0 == $responsableId) {
			$loginResponsable = $VARS->post('nouveauLogin');
			if ('' == $loginResponsable) {
				die('Pas de responsable pour les invités');
				return;
			}
			// Créer un nouveau compte
			$responsableId = $this->m_db->insert('users', array(
				'login' => $loginResponsable,
				'password' => Visitor::$DEFAULT_PASSWORD,
				'allowed_pages' => '1,2,3'
			));
		}

		for ($i = 0; $i < $nbInvites; ++$i) {
			Invite::ajouter(array(
				'nom' => $noms[$i],
				'prenom' => $prenoms[$i],
				'official_id' => $responsableId
			));
		}
	}

	public function changerStatut() {
		global $VARS;

		$this->m_db->update('invites',
			array('statut', $VARS->get('statut', 'int')),
			'id=' . $VARS->get('idInvite', 'int'));
	}

	// Méthodes pour les différentes vues

	/**
	 * Génère la liste complète des invités
	 * Si aucun invité n'est enregistré, on renvoie un message plutôt que la liste.
	 *
	 * @return string contenant la liste
	 */
	public function listView() {
		if (0 < $this->m_db->select('invites', '*')) {
			$content = '';
			while ($dataInvite = $this->m_db->fetch()) {
				$invite = Invite::getByData($dataInvite);
				$content .= $invite->renderLine();
			}

			return '<ul>' . $content . '</ul>';
		} else {
			return '<p>Aucun invité pour le moment</p>';
		}
	}

	public function registrationView($nbParticipants = 2) {
		$page = new Pager('RegistrationForm', false);

		$form = new Form('registration');
		$users = array(0 => '--');
		$this->m_db->select('users', 'id, login', "login!='anonymous'");
		while ($user = $this->m_db->fetch()) {
			$users[$user['id']] = $user['login'];
		}
		$responsable = $form->input('nouveauLogin', 'Responsable') . '&nbsp;';
		$responsable .= $form->select('responsable', '', $users, 0) . '</br>';

		$participants = '';
		for ($i = 0; $i < $nbParticipants; ++$i) {
			$participantForm = new Form('registration', $i);

			$participant = "<div class='participant'>";
			$participant .= $participantForm->input('nom', 'Nom') . '</br>';
			$participant .= $participantForm->input('prenom', 'Prenom');

			$participants .= $participant . '</div>';
		}

		$page->content("
			{$form->create('listing.php', 'registration')}
			$responsable
			$participants
			{$form->hidden('action', 'registration')}
			{$form->submit('', 'Enregistrer')}
			{$form->end()}"
		);

		return $page->renderComponent();
	}

	public function deletionView() {

	}

	// Fonctions élémentaires
}
