<?php

class Invite {
	private $m_db;
	private $m_visitor;
	private $m_data;
	private $_editable;

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
		$this->_editable = ($this->m_visitor->id() == $this->m_data['official_id']
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

	private function getStatus($statusCode) {
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

	/**
	 * Génère le contenu d'une ligne dans le tableau pour un utilisateur
	 *
	 * @return string contenant la ligne en HTML
	 */
	public function renderLine() {
		$content = "{$this->m_data['nom']} {$this->m_data['prenom']}";
		$content.= " <span class='status_{$this->m_data['id']}'>(";
		$content.= $this->getStatus($this->m_data['statut']);
		$content.= ")</span>";

		if ($this->m_visitor->id()==$this->m_data['official_id']
				|| $this->m_visitor->isAdmin()) {
			$form = new Form('statusUpdate');
			$content.= ' '.$form->select('status_'.$this->m_data['id'], '', array(
					'0' => 'Indécis',
					'1' => 'Présent',
					'-1' => 'Absent',
				), $this->m_data['statut']);
		}

		return '<li>'.$content.'</li>';
	}

	public function changerStatut($nouveauStatut) {
		if ($this->_editable) {
			$this->m_db->update('invites',
				array('statut' => $nouveauStatut),
				'id=' . $this->m_data['id']);

			return $this->getStatus($nouveauStatut);
		}
		else {
			return $this->getStatus($this->m_data['statut']);
		}

	}
}
