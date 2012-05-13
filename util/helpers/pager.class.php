<?php

class Pager {
	private $visitor;
	private $page;
	private $m_title;
	private $m_pageTitle;
	private $m_content;
	private $css;
	private $js;
	
	static private $includePath = 'include/';
	static public $baseUrl = 'mariage/';

	public function __construct($page) {
		global $VISITOR;
		
		$this->visitor = $VISITOR;
		$this->page = $page;
		$this->m_title = '';
		$this->m_pageTitle = '';
		$this->m_content = '';
		$this->css = array();
		$this->js = array(
				'javascript/jquery-1.7.2.min.js'
			);

		$this->visitor->manageConnection();
		ob_start();
	}

	public function __set($attribut, $valeur) {
		$this->{'m_' . $attribut} = $valeur;
	}

	public function __get($attribut) {
		return $this->{'m_' . $attribut};
	}
	
	public function addCss($file) {	
		if (is_array($file)) {
			$this->css = array_merge($this->css, $file);
		}
		else {
			$this->css[] = $file;
		}
	}
	
	public function addJs($file) {
		if (is_array($file)) {
			$this->js = array_merge($this->js, $file);
		}
		else {
			$this->js[] = $file;
		}
	}

	static public function generateHeaders($pageTitle, $css=array()) {
		$headerScripts = '';
		foreach ($css as $header) {
			$headerScripts.= "<link rel='stylesheet' href='$header' type='text/css'/>\n";
		}
		
		return <<<HEADERS
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>$pageTitle</title>
	{$headerScripts}
</head>
<body>		
HEADERS;
	}

	static public function generateFooter($scripts=array()) {
		$htmlScripts = '';
		foreach ($scripts as $script) {
			$htmlScripts.= "<script type='text/javascript' src='$script'></script>";
		}
		
		return <<<FOOTER
</body>
{$htmlScripts}
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
	<li><a href="admin.php">Admin page</a></li>
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
	
	public function visible() {	return $this->visitor->hasAccess($this->page);	}
	
	// Génération de la page complète

	function render() {
		$this->m_content = ob_get_contents();
		ob_end_clean();
		
		if (!$this->visitor->hasAccess($this->page)) {
			$this->renderDenied();
		}
		
		ob_start();
			require dirname(__FILE__).'/../../vues/layout.php';
			$content = ob_get_contents();
		ob_end_clean();
		
		echo $content;
	}

	private function renderGranted() {
		//$content = self::generateHeaders($this->m_title, $this->css);

// 		$content .= "<h1>{$this->m_pageTitle}</h1>";
// 		$content .= $this->connexionForm();
// 		$content .= $this->generateMenu();
// 		$content .= "<div id='content'>{$this->m_content}</div>";
// 		$content .= self::generateFooter($this->js);

		ob_start();
			require dirname(__FILE__).'/../../vues/layout.php';
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}

	private function renderDenied() {
		$this->m_title = 'Accès refusé';
		$this->m_pageTitle = 'Page à accés restreint';
		$this->m_content = '';
	}
	
	// Génération d'un composant de la page

	function renderComponent($id = '', $class = '') {
		if ($this->visitor->hasAccess($this->page)) {
			if (''==$id || ''==$class) {
				return $this->m_content;
			}
			else if (''!=$id) {
				return "<div id='$id'>{$this->m_content}</div>";
			}
			else {
				return "<div class='$class'>{$this->m_content}</div>";
			}
		}
	}
	
	// Méthodes statiques
	
	static public function includePart($path) {
		include self::$includePath.$path;
	}
	
	static public function url($page) {
		return $page;
	}
	
	static public function redirect($page) {
		header('Location:'.self::url($page));
	}
}
