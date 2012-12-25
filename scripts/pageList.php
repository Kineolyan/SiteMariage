<?php

class Page {
	private $_name;
	private $_title;
	
	public function __construct($name, $title) {
		$this->_name = $name;
		$this->_title = $title;
	}
	
	public function title() { return $this->_title; }
	public function name() { return $this->_name; }
}

// $pagesList[<fichier>] = <titre>
$pagesList = array(
		'Index' => new Page('Entrée', "Lien vers l'image d'accueil"),
		'Accueil' => new Page('Accueil', "Page d'accueil du site"),
		'Infos' => new Page('Informations', "Informations diverses sur la réception"),
		'Contact' => new Page('Contact', "Comment nous contacter"),
		'Listing' => new Page('Liste des invités', "Gestion de la liste des invités"),
		'Stats' => new Page('Statistiques', "Statistiques sur plusieurs points du mariage"),
		'Facture' => new Page('Facture', "Facture et autres informations"),
		'Admin' => new Page('Administration', "Zone d'administration")
);

$componentsList = array(
		'RegistrationForm', 'EditionForm'
);

?>
