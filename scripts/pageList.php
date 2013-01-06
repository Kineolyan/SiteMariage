<?php

class Page {
	private $_file;
	private $_name;
	private $_title;
	
	public function __construct($file, $name, $title) {
		$this->_file = $file;
		$this->_name = $name;
		$this->_title = $title;
	}
	
	public function file() { return $this->_file; }
	public function title() { return $this->_title; }
	public function name() { return $this->_name; }
	
	public function fileName() {
		$fileName = $this->_file;
		$fileName[0] = strtolower($fileName[0]);
		
		return $fileName . '.php';
	}
}

$pagesList = array(
		new Page('Index', 'Entrée', "Lien vers l'image d'accueil"),
		new Page('Accueil', 'Accueil', "Page d'accueil du site"),
		new Page('Infos', 'Informations', "Informations diverses sur la réception"),
		new Page('Contact', 'Contact', "Comment nous contacter"),
		new Page('Listing', 'Liste des invités', "Gestion de la liste des invités"),
		new Page('Stats', 'Statistiques', "Statistiques sur plusieurs points du mariage"),
		new Page('Facture', 'Facture', "Facture et autres informations"),
		new Page('Admin', 'Administration', "Zone d'administration")
);

$componentsList = array(
		'RegistrationForm', 'EditionForm', 'MySQL'
);

?>
