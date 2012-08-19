<?php

require_once 'invite.class.php';

class Liste {
	private $_db;
	private $_visitor;

	public function __construct() {
		global $DB, $VISITOR;

		$this->_db = $DB;
		$this->_visitor = $VISITOR;
	}

	// Méthodes de gestion des invités

	public function gererSoumission() {
		global $VARS;

		switch ($VARS->post('action')) {
		case 'registration':
			$this->registerGuests();
			Pager::redirect('listing.php?view=liste');
			break;

		case 'edition':
			$this->updateGuests();
			Pager::redirect('listing.php?view=liste');
			break;

		case 'categorize':
			$this->updateCategories();
			Pager::redirect('listing.php?view=categories');
			break;
		}
	}

	private function registerGuests() {
		global $VARS;

		$noms = $VARS->post('nom');
		$prenoms = $VARS->post('prenom');
		$nbInvites = count($noms);

		// Récupérer l'id du responsable
		$responsableId = $VARS->post('responsable', 'int');
		if (NULL == $responsableId || 0 == $responsableId) {
			$loginResponsable = $VARS->post('nouveauLogin');
			if (NULL == $loginResponsable || '' == $loginResponsable) {
				$VARS->setFlash("erreur", 'Pas de responsable pour les invités');
				return;
			}
			// Créer un nouveau compte
			$responsableId = $this->_db->insert('users', array(
				'login' => $loginResponsable,
				'password' => Visitor::$DEFAULT_PASSWORD,
				'allowed_pages' => '1,2,3'
			));
		}

		$erreurs = array();
		for ($i = 0; $i < $nbInvites; ++$i) {
			if ("" != $noms[$i] && "" != $prenoms[$i]) {
				$res = Invite::ajouter(array(
					'nom' => $noms[$i],
					'prenom' => $prenoms[$i],
					'official_id' => $responsableId,
					'plus_un' => $responsableId
				));
				if (!$res) {
					$erreurs[] = "{ $noms[$i] $prenoms[$i] à l'ajout }";
				}
			}
			else {
				$erreurs[] = "{ $noms[$i] $prenoms[$i] }";
			}
		}
		if (!empty($erreurs)) {
			$VARS->setFlash("erreur", 'Enregistrements impossibles pour '.implode(', ', $erreurs));
		}
	}

	private function updateGuests() {
		global $VARS;
		$erreur = array();

		$id = $VARS->post('id', 'int');
		$nom = $VARS->post('nom', 'string');
		$prenom = $VARS->post('prenom', 'string');
		$enfant = $VARS->post('enfant', 'int');

		$invite = Invite::getById($id);
		if ($invite->estEditable()) {
			if ('' != $nom && '' != $prenom) {
				if (!$invite->mettreAJour(array('nom'=> $nom, "prenom"=>$prenom))) {
					$erreurs[] = "Erreur sur les données de mise à jour.";
				}
			} else {
				$erreurs[] = "Données non conforme pour l'invité.";
			}

			if (0 == $enfant && $invite->aCategorie('enfant')) {
				// Enlever la catégorie Enfant
				$this->_db->delete('mm_user_categorie',
						"user_id={$invite->id} AND categorie_id=".Categories::id('enfant'));
			} else if (1 == $enfant && !$invite->aCategorie('enfant')) {
				// Ajouter catégorie enfant
				$this->_db->insert('mm_user_categorie', array(
						'user_id' => $invite->id, 'categorie_id' => Categories::id('enfant')));
			}
		}
		else {
			$erreurs[] = "Impossible d'éditer cet invité.";
		}

		$plusUnChoice = $VARS->post('plusUnChoice', 'int');
		$plusUnId = $VARS->post('plusUnId', 'int');
		if (-2 == $plusUnChoice && -1 != $plusUnId) {
			// Supprimer le plus un
			$res = $invite->changerPlusUn($plusUnId, false);
			if ('' != $res) {
				$erreurs[] = $res;
			}
		} else if (-1 != $plusUnChoice) {
			// Changer le plus un
			$res = $invite->changerPlusUn($plusUnChoice, true);
			if ('' != $res) {
				$erreurs[] = $res;
			}
		} else if (-1 != $plusUnId) {
			// Editer le plus un
			Invite::getById($plusUnId)->mettreAJour(array(
					'nom'=> $VARS->post('plusUnNom', 'string'),
					"prenom"=>$VARS->post('plusUnPrenom', 'string')
			));
		} else {
			// Créer un nouvel invité
			$nomPlusUn = $VARS->post('plusUnNom', 'string');
			$prenomPlusUn = $VARS->post('plusUnPrenom', 'string');
			if ("" != $nomPlusUn && "" != $prenomPlusUn) {
				$res = Invite::ajouter(array(
						'nom' => $nomPlusUn,
						'prenom' => $prenomPlusUn,
						'official_id' => $invite->id
				));
				if (!$res) {
					$erreurs[] = "{ $nomPlusUn $prenomPlusUn à l'ajout }";
				}
			}
		}

		if (!empty($erreurs)) {
			$VARS->setFlash("erreur", implode('<br/>', $erreurs));
		}
	}

	public function changerStatut() {
		global $VARS;

		$this->_db->update('invites',
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
		if (0 < $this->_db->select('invites', '*')) {
			$content = '';
			while ($dataInvite = $this->_db->fetch()) {
				$invite = Invite::getByData($dataInvite);
				$content .= $invite->renderLine();
			}
			$this->_db->endQuery();

			return '<table id="invites" class="table table-striped table-condensed">' . '<thead>'
					. Invite::renderLineHeader() . '</thead>' . '<tbody>' . $content . '</tbody>'
					. '</table>';
		} else {
			$this->_db->endQuery();
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
		if (0 < $this->_db->select('invites', '*', 'official_id=' . $this->_visitor->id())) {
			$content = '';
			while ($dataInvite = $this->_db->fetch()) {
				$invite = Invite::getByData($dataInvite);
				$content .= $invite->renderLine();
			}
			$this->_db->endQuery();

			return '<table id="invites" class="table table-striped table-condensed">'
				.'<thead>' . Invite::renderLineHeader() . '</thead>'
				.'<tbody>' . $content . '</tbody>'
				. '</table>';
		} else {
			$this->_db->endQuery();
			return '<p>Aucun invité pour le moment</p>';
		}
	}

	public function registrationView($nbParticipants = 1) {
		$page = new Pager('RegistrationForm', false);

		$form = new Form('registration');
		$users = array(0 => '--');
		$this->_db->select('users', 'id, login', "login!='anonymous'");
		while ($user = $this->_db->fetch()) {
			$users[$user['id']] = $user['login'];
		}
		$this->_db->endQuery();

		$responsable = "";
		if ($this->_visitor->isAdmin()) {
			$responsable .= $form->select('responsable', 'Responsable', $users, 0) . '&nbsp;';
			$responsable .= " ou un nouveau responsable: " . $form->input('nouveauLogin', '')
					. '</br>';
		} else {
			$responsable .= $form->hidden('responsable', $this->_visitor->id());
		}

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

		$page->content("{$form->create('listing.php', 'registration')}
			$responsable
			$participants
			{$form->hidden('action', 'registration')}
			{$form->submit('', 'Enregistrer')}
			{$form->end()}");

		return $page->renderComponent();
	}

	public function editionView() {
		global $VARS;

		$page = new Pager('EditionForm', false);

		$invite = Invite::getById($VARS->get('id', 'int'));
		if ($invite->estEditable()) {
			$aPlusUn = false;
			$form = new Form('edition');

			$formHtml = $form->create('listing.php', 'edition');

			$inviteHtml = '<h2>Invité</h2>';
			$inviteHtml.= $form->hidden("id", $invite->id);
			$inviteHtml.= $form->input("nom", "Nom", $invite->nom) .'<br/>';
			$inviteHtml.= $form->input("prenom", "Prenom", $invite->prenom) .'<br/>';


			$inviteHtml.= $form->check("enfant", "Est un enfant", '1', $invite->aCategorie('enfant'));

			if (-1 == $invite->plus_un) {
				$plusUn = Invite::getByQuery('plus_un='.$invite->id);

				$plusUnHtml = '<h2>Accompagné de</h2>';

				$possiblesPlusUn = array('-1' => '--', '-2' => '-- supprimer --');
				if (0 < $this->_db->select('invites', 'id, nom, prenom',
						"plus_un=-1 AND id!={$invite->id} AND id NOT IN (SELECT DISTINCT plus_un FROM invites WHERE plus_un != -1)",
						array('orderBy' => 'nom ASC, prenom ASC'))) {
					while ($possible = $this->_db->fetch()) {
						$possiblesPlusUn[$possible['id']] = "$possible[nom] $possible[prenom]";
					}
				}
				$this->_db->endQuery();

				$plusUnHtml.= $form->hidden("plusUnId", NULL!=$plusUn ? $plusUn->id: "-1");
				$plusUnHtml.= $form->input("plusUnNom", "Nom", NULL!=$plusUn ? $plusUn->nom : '') .'<br/>';
				$plusUnHtml.= $form->input("plusUnPrenom", "Prenom", NULL!=$plusUn ? $plusUn->prenom : '') .'<br/>';
				$plusUnHtml.= $form->select('plusUnChoice', '', $possiblesPlusUn, '--');
			}
			else {
				$hote = Invite::getById(intval($invite->plus_un));

				$plusUnHtml = '<h2>Accompagne</h2>';
				$plusUnHtml.= "<p><a href='listing.php?view=edition&id={$hote->id}'>"
					."{$hote->nom} {$hote->prenom}</a></p>";
			}

			$formHtml.= $form->hidden('action', 'edition')
				."<div class='row'>"
				."<div class='span5'>$inviteHtml</div>"
				."<div class='span5'>$plusUnHtml</div>"
				."</div>"
				.$form->submit('', 'Enregistrer')
				.$form->end();
			$page->content($formHtml);
		}
		else {
			$page->content("Vous n'avez pas le droit d'éditer cette personne");
		}

		return $page->renderComponent();
	}

	public function deletionView() {
	/* -- Categories -- */

	public function categoriesView() {
		$form = new Form('CategoriesForm');

		$headers = '<tr><th></th>';
		foreach (Categories::getCategories() as $id => $categorie) {
			$headers .= "<th class='check' category='$categorie'>$categorie</th>";
		}
		$headers .= '</tr>';

		$rows = array();
		if (0 < $this->_db->select('invites', 'id', '', array('orderBy' => 'nom ASC, prenom ASC'))) {
			while ($inviteData = $this->_db->fetch()) {
				$invite = Invite::getById(intval($inviteData['id']));

				$row = "<tr><td>{$invite->nom} {$invite->prenom}</td>";
				foreach (Categories::getCategories() as $id => $categorie) {
					$row .= "<td class='check' category='$categorie'>"
							. $form->check("links[$invite->id][$id]", "", "",
									$invite->aCategorie($categorie)) . "</td>";
				}
				$row .= '</tr>';

				$rows[] = $row;
			}
		}

		$formHtml = $form->create('listing.php', 'categoriesForm');
		$formHtml .= "<table id='categoriesTable' class='table table-striped'>" . '<thead>'
				. $headers . '</thead>' . '<tbody>' . implode("\n", $rows) . '</tbody></table>';
		$formHtml .= $form->hidden('action', 'categorize');
		$formHtml .= $form
				->submit('', 'Enregistrer les catégories', array('class' => 'btn btn-success'));
		$formHtml .= $form->end();

		return $formHtml;
	}

	public function updateCategories() {
		global $VARS;

		$nbErreurs = 0;
		$categoriesIds = array_keys(Categories::getCategories());
		$links = $VARS->post('links', 'array');

		$this->_db->select('invites', 'id', '');
		while ($invitesData = $this->_db->fetch()) {
			try {
				$inviteId = intval($invitesData['id']);
				if (!isset($links[$inviteId])) {
					$this->_db->delete('mm_user_categorie', "user_id=$inviteId");
					continue;
				}

				$selectedCategories = $links[$inviteId];
				$invite = Invite::getById($inviteId);

				// Trouver les modifications
				$suppressions = array();
				foreach ($categoriesIds as $categorieId) {
					if ($invite->aCategorie($categorieId) && !isset($selectedCategories[$categorieId])) {
						$suppressions[] = "(user_id={$invite->id} AND categorie_id=$categorieId)";
					} else if (!$invite->aCategorie($categorieId)
							&& isset($selectedCategories[$categorieId])) {
						// Ajouter une nouvelle categorie pour l'invité
						$this->_db ->insert('mm_user_categorie',
										array('user_id' => $invite->id, 'categorie_id' => $categorieId));
					}
				}

				// on supprime tous en une fois
				if (0 < count($suppressions)) {
					$this->_db->delete('mm_user_categorie', implode(' OR ', $suppressions));
				}
			} catch (Exception $e) {
				++$nbErreurs;
				$VARS->erreur('Mise à jour impossible pour l\'invite d\'id ' . $inviteId);
				continue;
			}

		}
		$this->_db->endQuery();

		if (0 == $nbErreurs) {
			$VARS->succes('Mise à jour de tous les invités réussie.');
		}
	}
	// Fonctions élémentaires
}
