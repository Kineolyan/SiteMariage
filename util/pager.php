<?php

include_once 'visitor.php';

class Pager {
	private $visitor;
	private $page;
	private $m_title;
	private $m_pageTitle;
	private $m_content;

	public function __construct($visitor, $page) {
		$this->visitor = $visitor;
		$this->page = $page;
		$this->m_title = '';
		$this->m_pageTitle = '';
		$this->m_content = '';

		$this->visitor->manageConnection();
	}

	public function __set($attribut, $valeur) {
		$this->{'m_' . $attribut} = $valeur;
	}

	static public function generateHeaders($pageTitle) {
		return <<<HEADERS
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>$pageTitle</title>
</head>
<body>		
HEADERS;
	}

	static public function generateFooter() {
		return <<<FOOTER
</body>
</html>
FOOTER;
	}

	static public function generateMenu() {
		return <<<MENU
<h2>Menu :</h2>
<ul>
	<li><a href="index.php">Accueil</a></li>
	<li><a href="infos.php">Informations</a></li>
	<li><a href="listing.php">Listing</a></li>
	<li><a href="facture.php">Facture</a></li>
</ul>	
MENU;
	}

	private function connexionForm() {
		if ($this->visitor->isLogged()) {
			return <<<CONNECTION
<form action='' method='post'>
	<p>
		{$this->visitor->nom()}&nbsp;
		<input type='submit' name='deconnexion' value='Déconnecter'/>
	</p>
</form>
CONNECTION;
		} else {
			return <<<CONNECTION
<form action='' method='post'>
	<p>
		<label for='connect_login'>Login :
		<input type='text' id='connect_login' name='login'/>&nbsp;
		<label for='connect_password'>Mot de passe :
		<input type='password' id='connect_password' name='password'/>&nbsp;
		<input type='submit' name='connexion' value='Connecter'/>
	</p>
</form>
CONNECTION;
		}
	}

	function render() {
		echo $this->visitor->hasAccess($this->page)?
			$this->renderGranted():
			$this->renderDenied();
	}

	private function renderGranted() {
		$content = self::generateHeaders($this->m_title);

		$content .= "<h1>{$this->m_pageTitle}</h1>";
		$content .= $this->connexionForm();
		$content .= $this->generateMenu();
		$content .= "<div id='content'>{$this->m_content}</div>";
		$content .= self::generateFooter();
		
		return $content;
	}

	private function renderDenied() {
		$content = self::generateHeaders('Accès refusé');

		$content.= <<<BODY
<h1>Page à accés restreint</h1>
<p>Retour à l'<a href="index.php">Accueil</a></p>
BODY;
		$content.= self::generateFooter();
		
		return $content;
	}
}
