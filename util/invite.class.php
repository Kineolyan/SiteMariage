<?php

class Invite {
	private $_db;
	private $_visitor;
	private $_data;
	private $_editable;
	private $_categories;

	private $_id;

	private function __construct($id, $data) {
		global $DB, $VISITOR;

		$this->_db = $DB;
		$this->_visitor = $VISITOR;
		if (is_int($id)) {
			$this->_data = $this->_db->get('invites', '*', "id=".$id);
		}
		else if (is_array($data)) {
			$this->_data = $data;
		}
		else {
			throw new Exception("Impossible de créer un invité sans id ou données {id = $id, data = $data}");
		}

		$this->_id = array_key_exists('id', $this->_data) ? intval($this->_data['id']) : -1;

		$this->_editable = ($this->_visitor->id() == $this->_data['official_id']
				|| $this->_visitor->isAdmin());

 		$this->getCategories();
	}

	/**
	 * Renvoie une nouvelle instance d'invité créée par son id
	 * @param int $id est l'id de l'invité
	 * @return Invite demande ou NULL en cas d'erreur
	 */
	static public function getById($id) {
		global $LOGGER;

		try {
			return new Invite($id, NULL);
		} catch (Exception $e) {
			$LOGGER->error(sprintf("Creation de l'invite %d impossible : %s", intval($id), $e->getMessage()));
			return NULL;
		}
	}

	/**
	 * Renvoie une nouvelle instance d'invité créée à partir de ses données
	 * @param array $data
	 * @return Invite demande ou NULL en cas d'erreur
	 */
	static public function getByData($data) {
		global $LOGGER;

		try {
			return new Invite(NULL, $data);
		} catch (Exception $e) {
			$LOGGER->error(sprintf("Creation de l'invite suivant les data impossible : %s", $e->getMessage()));
			return NULL;
		}
	}

	/**
	 * Renvoie une nouvelle instance d'invité créée à partir d'une requête
	 * @param {String} $query est la requête pour trouver l'invité
	 */
	static public function getByQuery($query) {
		global $DB;

		$id = $DB->getSingleDatum('invites', 'id', $query);
		return NULL !== $id ? new Invite(intval($id), NULL) : NULL;
	}

	public function __get($attribut) {
		return isset($this->_data[$attribut])? $this->_data[$attribut]: NULL;
	}

	/**
	 * Prevents from editing an object with magic __set
	 */
	public function __set($attribut, $value) {}

	private function getCategories() {
		$this->_categories = array();
		if ($this->_db->select(
				'mm_user_categorie JOIN categories ON categories.id = mm_user_categorie.categorie_id',
				'mm_user_categorie.categorie_id AS id, categories.categorie',
				 'user_id='.$this->_id)) {
			while ($data = $this->_db->fetch()) {
				$this->_categories[$data['id']] = $data['categorie'];
			}
		}
		$this->_db->endQuery();
	}

	/**
	 * Détermine si l'invité fait partie d'une catégorie
	 *
	 * @param {mixed} $categorie:
	 * 	int pour l'id de la catégorie
	 * 	string pour le nom de la catégorie
	 *
	 * @return {boolean} true si l'invité fait partie de la catégorie
	 */
	public function aCategorie($categorie) {
		if (is_int($categorie)) {
			return array_key_exists($categorie, $this->_categories);
		} else if (is_string($categorie)) {
			return in_array($categorie, $this->_categories);
		}

		return false;
	}

	static public function ajouter($data) {
		global $DB, $LOGGER;

		if (Invite::verifierDonnees($data)) {
			// Ajout du timestamp
			$data['last_modification'] =  'NOW()';

			if (0 == $DB->count('invites', 'id',
					'nom="'.Variables::sanitize($data['nom']).'" AND prenom="'.Variables::sanitize($data['prenom']).'"')) {
				$idAjout = $DB->insert('invites', $data);
				$LOGGER->log(sprintf("Ajout d'un invité %s %s", Variables::sanitize($data['nom']), Variables::sanitize($data['prenom'])));

				return self::getById($idAjout);
			}
		}

		return NULL;
	}

	public function mettreAJour($data) {
		global $LOGGER;

		if (Invite::verifierDonnees($data)) {
			// Ajout du timestamp
			$data['last_modification'] =  'NOW()';

			$this->_db->update('invites', $data, 'id='.$this->_id);
			$LOGGER->log(sprintf("Mise à jour de %s", $this->_id));
			return true;
		}
		return false;
	}

	static private function verifierDonnees($data) {
		$requiredFields = array('nom', 'prenom');

		foreach ($requiredFields as $field) {
			if (!isset($data[$field]) || '' == $data[$field]) {
				return false;
			}
		}

		return true;
	}

	public static function getStatus($statusCode) {
		switch ($statusCode) {
			case 0:
				return 'Indécis';

			case 1:
				return 'Présent';

			case -1:
				return 'Absent';

			default:
				return 'Inconnu';
		}
	}

	public static function getStatusCode($status) {
		switch ($status) {
			case 'Présent':
				return 1;

			case 'Absent':
				return -1;

			case 'Indécis':
			default:
				return 0;
		}
	}

	public static function getStatusClass($status) {
		switch ($status) {
			case -1:
			case 'Absent':
				return 'btn-danger';

			case 1:
			case 'Présent':
				return 'btn-success';

			case 0:
			case 'Indécis':
			default:
				return 'btn-warning';
		}
	}

	/**
	 * Génère la ligne de header pour le tableau de invités
	 *
	 * @return string contenant le thead en HTML
	 */
	public static function renderLineHeader() {
		$content = "<th>Nom</th>";
		$content.= "<th>Prenom</th>";
		$content.= "<th>Statut</th>";

		return '<tr>'.$content.'</tr>';
	}

	/**
	 * Génère le contenu d'une ligne dans le tableau pour un utilisateur
	 *
	 * @return string contenant la ligne en HTML
	 */
	public function renderLine() {
		$content = "<td>{$this->_data['nom']}</td>";
		$content.= "<td>{$this->_data['prenom']}</td>";

		$statut = "<div class='btn-group'>
			<button class='btn ".Invite::getStatusClass($this->_data['statut'])." dropdown-toggle' data-toggle='dropdown'>";
		$statut.= "<span statusId='{$this->_id}'>";
		$statut.= $this->getStatus($this->_data['statut']);
		$statut.= '</span>';

		$accompagne = '';
		if (-1 != $this->_data['plus_un']) {
			$accompagne = '<i class="icon-plusUn"></i>';
		} else  if (0 < $this->_db->count('invites', 'id', 'plus_un='.intval($this->_id))) {
			$accompagne = '<i class="icon-avecPlusUn"></i>';
		}

		$editionBtn = "";

		if ($this->estEditable()) {
			$statut.= '&nbsp;<b class="caret"></b></button>';
			$statut.= '<ul class="dropdown-menu">
				<li>Indécis</li>
				<li>Présent</li>
				<li>Absent</li>
				</ul>';

			$editionBtn = "<a href='listing.php?view=edition&id={$this->_id}'><i class='icon-pencil'></i></a>";
		}

		$statutInvitation = "";
		if (0 != $this->_data['invitation_send']) {
			$statutInvitation = "<i class='icon-faire-part'></i>";
		}
		
		$statut.= "</button></div>";
		$content.= "<td class='btn-toolbar'>"
			."<div class='actions btn-group'>$editionBtn $accompagne</div>"
			." $statut $statutInvitation</td>";

		return '<tr>'.$content.'</tr>';
	}

	/**
	 * Détermine si l'invité peut être éditer par le visiteur courant
	 *
	 * @return true si le visiteur peut éditer
	 */
	public function estEditable() {
		return $this->_editable;
	}

	public function changerStatut($nouveauStatut) {
		global $LOGGER;

		$statut = Invite::getStatusCode($nouveauStatut);

		if ($this->_editable && NULL !== $statut) {
			if ($statut != $this->_data['statut']) {
				$this->_db->update('invites',
					array('statut' => $statut),
					'id=' . $this->_id);
				
				$LOGGER->log(sprintf("Le statut de %s change : %s -> %s"
					, $this->_id, $this->_data['statut'], $statut));
				$this->_data['statut'] = $statut;	
			}

			return $nouveauStatut;
		}
		else {
			return Invite::getStatus($this->_data['statut']);
		}

	}

	public function envoyerInvitation($envoyer) {
		global $LOGGER;

		$dbEnvoi = 'invitation_send';

		if ($this->_editable) {
			$statutEnvoi = $envoyer? 1: 0;

			if ($statutEnvoi != $this->_data[$dbEnvoi]) {
				$this->_db->update('invites',
					array($dbEnvoi => $statutEnvoi),
					'id=' . $this->_id);

				$LOGGER->log(sprintf("%s de l'invation pour %s"
					, $envoyer? 'Envoi' : 'Récupération', $this->_id));
				$this->_data[$dbEnvoi] = $statutEnvoi;
			}

			return $statutEnvoi;
		}
		else {
			return Invite::getStatus($this->_data[$dbEnvoi]);
		}

	}

	/**
	 * Edite le plus un de l'invité.
	 *
	 * @param {int} $id est l'id du plus un
	 * @param {bool} $replace indique un remplacement du plus_un
	 */
	public function changerPlusUn($id, $replace) {
		global $LOGGER;

		try {
			$this->_db->update('invites', array('plus_un' => -1), "plus_un=".$this->_id);
			$LOGGER->log(sprintf("Suppression du plus-un de %s", $this->_id));
		} catch (Exception $e) {
			$LOGGER->error(sprintf("Impossible de supprimer le plus-un de %s", $this->_id));
			return "Impossible de supprimer l'ancien plus un.";
		}

		if ($replace) {
			try {
				$this->_db->update('invites', array('plus_un' => $this->_id), 'id='.$id);
				$LOGGER->log(sprintf("Changement du plus-un de %s pour %d", $this->_id, $id));
			} catch (Exception $e) {
				$LOGGER->error(sprintf("Impossible d'ajouter le plus-un %d de %s", $id, $this->_id));
				return 'Impossible de mettre à jour le plus un';
			}
		}

		return '';
	}
}
