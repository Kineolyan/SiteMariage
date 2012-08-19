<?php

class Invite {
	private $_db;
	private $_visitor;
	private $_data;
	private $_editable;
	private $_categories;

	private function __construct($id, $data) {
		global $DB, $VISITOR;

		$this->_db = $DB;
		$this->_visitor = $VISITOR;
		if (is_int($id)) {
			$this->_data = $this->_db->get('invites', '*', "id=".intval($id));
		}
		else if (is_array($data)) {
			$this->_data = $data;
		}
		else {
			throw new Exception("Impossible de créer un invité sans id ou données {id = $id, data = $data}");
		}
		$this->_editable = ($this->_visitor->id() == $this->_data['official_id']
				|| $this->_visitor->isAdmin());

 		$this->getCategories();
	}

	/**
	 * Renvoie une nouvelle instance d'invité créée par son id
	 * @param int $id est l'id de l'invité
	 */
	static public function getById($id) {	return new Invite($id, NULL);	}

	/**
	 * Renvoie une nouvelle instance d'invité créée à partir de ses données
	 * @param array $data
	 */
	static public function getByData($data) {	return new Invite(NULL, $data);	}

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
				 'user_id='.$this->_data['id'])) {
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
		global $DB;

		if (Invite::verifierDonnees($data)) {
			// Ajout du timestamp
			$data['last_modification'] =  'NOW()';

			if (0 == $DB->count('invites', 'id',
					'nom="'.Variables::sanitize($data['nom']).'" AND prenom="'.Variables::sanitize($data['prenom']).'"')) {
				$idAjout = $DB->insert('invites', $data);

				return self::getById($idAjout);
			}
		}

		return NULL;
	}

	public function mettreAJour($data) {
		if (Invite::verifierDonnees($data)) {
			// Ajout du timestamp
			$data['last_modification'] =  'NOW()';

			$this->_db->update('invites', $data, 'id='.$this->_data['id']);
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
	 * Génère le contenu d'une ligne dans le tableau pour un utilisateur
	 *
	 * @return string contenant la ligne en HTML
	 */
	public function renderLine() {
		$content = "<td>{$this->_data['nom']}</td>";
		$content.= "<td>{$this->_data['prenom']}</td>";

		$statut = "<div class='btn-group'>
			<button class='btn ".Invite::getStatusClass($this->_data['statut'])." dropdown-toggle' data-toggle='dropdown'>";
		$statut.= "<span statusId='{$this->_data['id']}'>";
		$statut.= $this->getStatus($this->_data['statut']);
		$statut.= '</span>';

		$accompagne = '';
		if (-1 != $this->_data['plus_un']) {
			$accompagne = '<i class="icon-plusUn"></i>';
		} else  if (0 < $this->_db->count('invites', 'id', 'plus_un='.intval($this->_data['id']))) {
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

			$editionBtn = "<a href='listing.php?view=edition&id={$this->_data['id']}'><i class='icon-pencil'></i></a>";
		}
		$statut.= "</button></div>";
		$content.= "<td class='btn-toolbar'><div class='actions btn-group'>$editionBtn $accompagne</div> $statut</td>";

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

	public static function renderLineHeader() {
		$content = "<th>Nom</th>";
		$content.= "<th>Prenom</th>";
		$content.= "<th>Statut</th>";

		return '<tr>'.$content.'</tr>';
	}

	public function changerStatut($nouveauStatut) {
		$statut = Invite::getStatusCode($nouveauStatut);

		if ($this->_editable && NULL !== $statut) {
			if ($statut != $this->_data['statut']) {
				$this->_db->update('invites',
					array('statut' => $statut),
					'id=' . $this->_data['id']);

				$this->_data['statut'] = $statut;
			}

			return $nouveauStatut;
		}
		else {
			return Invite::getStatus($this->_data['statut']);
		}

	}

	/**
	 * Edite le plus un de l'invité.
	 *
	 * @param {int} $id est l'id du plus un
	 * @param {bool} $replace indique un remplacement du plus_un
	 */
	public function changerPlusUn($id, $replace) {
		try {
			$this->_db->update('invites', array('plus_un' => -1), "plus_un=".$this->_data['id']);
		} catch (Exception $e) {
			return "Impossible de supprimer l'ancien plus un.";
		}

		var_dump($replace);
		if ($replace) {
			try {
				$this->_db->update('invites', array('plus_un' => $this->_data['id']), 'id='.$id);
			} catch (Exception $e) {
				return 'Impossible de mettre à jour le plus un';
			}
		}

		return '';
	}
}
