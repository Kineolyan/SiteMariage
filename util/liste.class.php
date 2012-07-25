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
				$VARS->setFlash("erreur", 'Pas de responsable pour les invités');
				return;
			}
			// Créer un nouveau compte
			$responsableId = $this->m_db->insert('users', array(
				'login' => $loginResponsable,
				'password' => Visitor::$DEFAULT_PASSWORD,
				'allowed_pages' => '1,2,3'
			));
		}

		$erreurs = array();
		for ($i = 0; $i < $nbInvites; ++$i) {
			if ("" != $noms[$i] && "" != $prenoms[$i]) {
				Invite::ajouter(array(
					'nom' => $noms[$i],
					'prenom' => $prenoms[$i],
					'official_id' => $responsableId
				));
			}
			else {
				$erreurs[] = "{ $noms[$i] $prenoms[$i] }";
			}
		}
		if (!empty($erreurs)) {
			$VARS->setFlash("erreur", 'Enregistrements impossibles pour '.implode(', ', $erreurs));
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

			return '<table id="invites" class="table table-striped table-condensed">'
				.'<thead>' . Invite::renderLineHeader() . '</thead>'
				.'<tbody>' . $content . '</tbody>'
				. '</table>';
		} else {
			return '<p>Aucun invité pour le moment</p>';
		}
	}

	/**
	 * Génère la liste des invités éditable par l'utilisateur
	 * Si aucun invité n'est enregistré, on renvoie un message plutôt que la liste.
	 *
	 * @return string contenant la liste
	 */
	public function personnalListView() {
		if (0 < $this->m_db->select('invites', '*', 'official_id='.$this->m_visitor->id())) {
			$content = '';
			while ($dataInvite = $this->m_db->fetch()) {
				$invite = Invite::getByData($dataInvite);
				$content .= $invite->renderLine();
			}

			return '<table id="invites" class="table table-striped table-condensed">'
				.'<thead>' . Invite::renderLineHeader() . '</thead>'
				.'<tbody>' . $content . '</tbody>'
				. '</table>';
		} else {
			return '<p>Aucun invité pour le moment</p>';
		}
	}

	public function registrationView($nbParticipants = 1) {
		$page = new Pager('RegistrationForm', false);

		$form = new Form('registration');
		$users = array(0 => '--');
		$this->m_db->select('users', 'id, login', "login!='anonymous'");
		while ($user = $this->m_db->fetch()) {
			$users[$user['id']] = $user['login'];
		}
		$responsable = $form->select('responsable', 'Responsable', $users, 0) . '&nbsp;';
		$responsable .= " ou un nouveau responsable: " . $form->input('nouveauLogin', '') . '</br>';

		$participants = '';
		for ($i = 0; $i < $nbParticipants; ++$i) {
			$participantForm = new Form('registration', $i);

			$participant = "<div class='participant row'>";
			$participant .= "<div class='input'>"
				. $participantForm->input('nom', 'Nom')
				. '</div>';
			$participant .= "<div class='input'>"
				. $participantForm->input('prenom', 'Prenom')
				. '</div>';
			$participant .= '</div>';

			$participants .= $participant;
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
