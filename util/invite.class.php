<?php

class Invite {
	private $m_db;
	private $m_visitor;
	private $m_data;
	
	private function __construct($id, $data) {
		global $DB, $VISITOR;
		
		$this->m_db = $DB;
		$this->m_visitor = $VISITOR;
		if (NULL==$data) {
			$this->m_data = $this->m_db->get('invite', '*', "id=$id");
		}
		else {
			$this->m_data = $data;
		}
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
	
	/**
	 * Génère le contenu d'une ligne dans le tableau pour un utilisateur
	 * 
	 * @return string contenant la ligne en HTML
	 */
	public function renderLine() {
		$content = "{$this->m_data['nom']} {$this->m_data['prenom']} ";
		switch ($this->m_data['statut']) {
			case 0:
				$content.= '(Indécis)';
				break;
				
			case 1:
				$content.= '(Présent)';
				break;
				
			case -1:
				$content.= '(Absent)';
				break;
				
			default:
				$content.= '(Inconnu)';
				break;	
		}
		
		if ($this->m_visitor->isAdmin()) {
			$content.= ' [Changer]';
		}
		
		return '<li>'.$content.'</li>';
	}
}
