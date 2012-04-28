<?php

include_once 'dba/dba.mysql.php';

class Liste {
	private $m_db;
	private $m_visitor;
	
	public function __construct() {
		global $DB, $VISITOR;
		
		$this->m_db = $DB;
		$this->m_visitor = $VISITOR;
	}
	
	
	/**
	 * Génère la liste complète des invités
	 * Si aucun invité n'est enregistré, on renvoie un message plutôt que la liste.
	 * 
	 * @return string contenant la liste
	 */
	public function listView() {
		if (0<$this->m_db->select('invites', '*')) {
			$content = '';
			while ($invite = $this->m_db->fetch()) {
				$content.= $this->renderLine($invite);
			}
			
			return '<ul>'.$content.'</ul>';
		}
		else {
			return '<p>Aucun invité pour le moment</p>';
		}
	}
	
	
	/**
	 * Génère le contenu d'une ligne dans le tableau pour un utilisateur
	 * 
	 * @param array $data contient les données sur l'utilisateur
	 * 
	 * @return string contenant la ligne en HTML
	 */
	private function renderLine($data) {
		$content = "$data[nom] $data[prenom] ";
		switch ($data['statut']) {
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
		
		return '<li>'.$content.'</li>';
	}
	
	public function ajouterInvite($data) {
		
	}
	
	public function changerStatut() {
		global $VARS;
		
		$this->m_db->update('invites',
			array('statut', $VARS->get('statut', 'int')), 
			'id='.$VARS->get('idInvite', 'int')
		);
	}
}