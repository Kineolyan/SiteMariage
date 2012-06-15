<?php

class Pager {
	private $m_visitor;
	private $m_page;
	private $m_title;
	private $m_pageTitle;
	private $m_content;
	private $m_css;
	private $m_js;

	static private $includePath = 'include/';
	static public $baseUrl = 'mariage/';

	/**
	 * Construit un patron pour la page et démarre l'enregistrement
	 * en buffer de toute sortie.
	 *
	 * @param String $page est le nom de la page
	 * @param boolean $capture indique s'il faut capturer toute sortie
	 */
	public function __construct($page, $capture = true) {
		global $VISITOR;

		$this->m_visitor = $VISITOR;
		$this->m_page = $page;
		$this->m_title = '';
		$this->m_pageTitle = '';
		$this->m_content = '';
		$this->m_css = array();
		$this->m_js = array(
				'javascript/jquery-1.7.2.min.js'
			);

		$this->m_visitor->manageConnection();
		if ($capture) {
			ob_start();
		}
	}

	private function getSet($attribut, $value) {
		if (NULL==$value) {
			return $this->{'m_'.$attribut};
		}
		else {
			$this->{'m_'.$attribut} = $value;
		}
	}

	public function title($value = NULL) {
		return $this->getSet('title', $value);
	}

	public function pageTitle($value = NULL) {
		return $this->getSet('pageTitle', $value);
	}

	public function content($value = NULL) {
		return $this->getSet('content', $value);
	}

	public function css() {	return $this->m_css;	}

	public function addCss($file) {
		if (is_array($file)) {
			$this->m_css = array_merge($this->m_css, $file);
		}
		else {
			$this->_css[] = $file;
		}
	}

	public function js() {	return $this->m_js;	}

	public function addJs($file) {
		if (is_array($file)) {
			$this->m_js = array_merge($this->m_js, $file);
		}
		else {
			$this->m_js[] = $file;
		}
	}

	public function getNavigation() {
		require 'scripts/pageList.php';

		$menu = '<ul>';
		foreach ($pagesList as $page => $pageTitle) {
			$pageName = $page;
			$pageName[0] = strtolower($pageName[0]);
			$menu.= "<li><a href='$pageName.php'>$pageTitle</a></li>";
		}
		$menu.= '</ul>';

		return $menu;
	}

	private function connexionForm() {
		if ($this->m_visitor->isLogged()) {
			return <<<CONNECTION
<form action='' method='post'>
	<p>
		{$this->m_visitor->nom()}&nbsp;
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

	public function visible() {	return $this->m_visitor->hasAccess($this->m_page);	}

	// Génération de la page complète

	function render() {
		$this->m_content = ob_get_contents();
		ob_end_clean();

		if (!$this->m_visitor->hasAccess($this->m_page)) {
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
		if ($this->m_visitor->hasAccess($this->m_page)) {
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

	function renderAjax() {
		if ($this->m_visitor->hasAccess($this->m_page)) {
			$this->m_content = ob_get_contents();
			ob_end_clean();

			echo $this->m_content;
		}
		else {
			echo '{state: "Access refused"}';
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
