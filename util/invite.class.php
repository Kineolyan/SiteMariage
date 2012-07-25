<?php

class Invite {
	private $m_db;
	private $m_visitor;
	private $m_data;
	private $m_editable;

	private function __construct($id, $data) {
		global $DB, $VISITOR;

		$this->m_db = $DB;
		$this->m_visitor = $VISITOR;
		if (NULL==$data) {
			$this->m_data = $this->m_db->get('invites', '*', "id=$id");
		}
		else {
			$this->m_data = $data;
		}
		$this->m_editable = ($this->m_visitor->id() == $this->m_data['official_id']
				|| $this->m_visitor->isAdmin());
	}

	static public function getById($id) {	return new Invite($id, NULL);	}

	static public function getByData($data) {	return new Invite(NULL, $data);	}

	public function __get($attribut) {
		return isset($this->m_data[$attribut])? $this->m_data[$attribut]: NULL;
	}

	static public function ajouter($data) {
		global $DB;

		$DB->insert('invites', $data);
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
		$content = "<td>{$this->m_data['nom']}</td>";
		$content.= "<td>{$this->m_data['prenom']}</td>";
		$content.= "<td><div class='btn-group'>
			<button class='btn ".Invite::getStatusClass($this->m_data['statut'])." dropdown-toggle' data-toggle='dropdown'>";
			$content.= "<span statusId='{$this->m_data['id']}'>";
			$content.= $this->getStatus($this->m_data['statut']);
			$content.= '</span>';

		if ($this->m_visitor->id()==$this->m_data['official_id']
				|| $this->m_visitor->isAdmin()) {
			$content.= '&nbsp;<b class="caret"></b></button>';
			$content.= '<ul class="dropdown-menu">
				<li>Indécis</li>
				<li>Présent</li>
				<li>Absent</li>
				</ul>';
		}
		$content.= "</button></div></td>";

		return '<tr>'.$content.'</tr>';
	}

	public static function renderLineHeader() {
		$content = "<th>Nom</th>";
		$content.= "<th>Prenom</th>";
		$content.= "<th>Statut</th>";

		return '<tr>'.$content.'</tr>';
	}

	public function changerStatut($nouveauStatut) {
		$statut = Invite::getStatusCode($nouveauStatut);

		if ($this->m_editable && NULL !== $statut) {
			if ($statut != $this->m_data['statut']) {
				$this->m_db->update('invites',
					array('statut' => $statut),
					'id=' . $this->m_data['id']);

				$this->m_data['statut'] = $statut;
			}

			return $nouveauStatut;
		}
		else {
			return Invite::getStatus($this->m_data['statut']);
		}

	}
}
