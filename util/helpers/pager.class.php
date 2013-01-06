<?php

class Pager {
	private $m_visitor;
	private $m_page;
	private $m_title;
	private $m_pageTitle;
	private $m_sousMenu;
	private $m_content;
	private $m_css;
	private $m_js;
	private $m_capture;
	private $m_authentificate;

	static private $includePath = 'include/';
	static public $baseUrl = 'mariage/';

	/**
	 * Construit un patron pour la page et démarre l'enregistrement
	 * en buffer de toute sortie.
	 *
	 * @param String $page est le nom de la page
	 * @param boolean $capture indique s'il faut capturer toute sortie
	 */
	public function __construct($page, $capture = true, $useAuthentification = true) {
		global $VISITOR;

		$this->m_visitor = $VISITOR;
		$this->m_page = $page;
		$this->m_headerTitle = '';
		$this->m_pageTitle = '';
		$this->m_sousMenu = NULL;
		$this->m_content = '';
		$this->m_css = array('bootstrap/css/bootstrap.min.css',
				'bootstrap/css/bootstrap-responsive.min.css', 'css/style.css');
		$this->m_js = array('javascript/jquery-1.7.2.min.js',
				'bootstrap/js/bootstrap.min.js', 'javascript/library.js',
				'javascript/general.js');
		$this->m_capture = $capture;
		$this->m_authentificate = $useAuthentification;

		if ($this->m_authentificate) {
			$this->m_visitor->manageConnection();
		}

		if ($this->m_capture) {
			ob_start();
		}
	}

	private function getSet(&$attribut, $value) {
		if (NULL == $value) {
			return $attribut;
		}
		else {
			$attribut = $value;
		}
	}

	public function pageId() {
		return strtolower($this->m_page);
	}

	public function headerTitle($value = NULL) {
		return $this->getSet($this->m_headerTitle, $value);
	}

	public function pageTitle($value = NULL) {
		return $this->getSet($this->m_pageTitle, $value);
	}

	public function sousMenu($items) {
		$this->m_sousMenu = $items;
	}

	public function content($value = NULL) {
		return $this->getSet($this->m_content, $value);
	}

	public function css() {
		return $this->m_css;
	}

	public function addCss($file) {
		if (is_array($file)) {
			$this->m_css = array_merge($this->m_css, $file);
		}
		else {
			$this->m_css[] = $file;
		}
	}

	public function js() {
		return $this->m_js;
	}

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

		$menu = '<span class="brand">C&amp;O</span><ul class="nav">';
		foreach ($pagesList as $page) {
			if ($this->m_visitor->hasAccess($page->file())) {
				$itemClass = $page->file() == $this->m_page ? 'active' : '';

				$menu .= "<li class='$itemClass'><a href=\"". $page->fileName() ."\" title=\"". Utils::escapeDblQuote($page->title()) ."\">"
					. $page->name() . "</a></li>";
			}
		}
		$menu .= '</ul>';

		return $menu;
	}

	private function connexionForm() {
		if ($this->m_visitor->isLogged()) {
			$connectedForm = new Form("connectedForm");
			$html = $connectedForm->create('', 'connectedForm');
			$html .= "<p>{$this->m_visitor->nom()}&nbsp;";
			$html .= $connectedForm->submit('deconnexion', 'Déconnecter');
			$html .= '</p>' . $connectedForm->end();

			return $html;
		}
		else {
			$connectionForm = new Form("connectionForm");
			$html = $connectionForm->create('', 'connectionForm', 'noJS');
			$html .= '<p>' . $connectionForm->input('login', 'Login : ') . '<br/>';
			$html .= $connectionForm->password('password', 'Mot de passe : ')
					. '<br/>';
			$html .= $connectionForm->submit('connexion', 'Connecter');
			$html .= '</p>' . $connectionForm->end();

			return $html;
		}
	}

	public function visible() {
		return $this->m_visitor->hasAccess($this->m_page);
	}

	// Génération de la page complète

	public function render($layout = NULL) {
		if ($this->m_capture) {
			$this->m_content = ob_get_contents();
			ob_end_clean();
		}

		// Ajouter le menu latéral s'il y a des éléments
		// 		if (NULL !== $this->m_sousMenu && 0 < count($this->m_sousMenu)) {
		// 			$menuLateral = '<ul class="nav nav-list">';
		// 			foreach ($this->m_sousMenu as $url => $section) {
		// 				if (is_array($section)) {
		// 					$menuLateral.= "<li class='nav-header'>$url</li>";
		// 					foreach ($section as $innerUrl => $titre) {
		// 						$menuLateral.= "<li><a href='$innerUrl'>$titre</a></li>";
		// 					}
		// 				}
		// 				else {
		// 					$menuLateral.= "<li><a href='$url'>$section</a></li>";
		// 				}
		// 			}
		// 			$menuLateral.= '</ul>';

		// 			$this->content = '<div class="span2">'.$menuLateral.'</div>'
		// 				.'<div class="span10">'.$this->m_content.'</div>';
		// 		}

		if ($this->m_authentificate && !$this->m_visitor->hasAccess($this->m_page)) {
			$this->renderDenied();
		}

		ob_start();
			if (NULL == $layout) {
				$layout = 'layout.php';
			}
			require dirname(__FILE__) . '/../../vues/' . $layout;
			$content = ob_get_contents();
		ob_end_clean();

		echo $content;
	}

	private function renderGranted() {
		ob_start();
		require dirname(__FILE__) . '/../../vues/layout.php';
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
			if ('' == $id || '' == $class) {
				return $this->m_content;
			}
			else if ('' != $id) {
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
		include self::$includePath . $path;
	}

	static public function url($page) {
		return $page;
	}

	static public function redirect($page) {
		header('Location:' . self::url($page));
	}

	static public function handleException($exception) {
		global $LOGGER;

		ob_end_clean();

		$LOGGER->log(sprintf('Exception générale : %s', $exception->getMessage()));
		
		try {
			$page = new Pager('Erreur', false, false);
			$page->headerTitle('Erreur');
			$page->pageTitle('Erreur sur la page');
			$page->content(<<<EOF
<p>
	Un problème technique a eu lieu au chargement de cette page.<br/>
	Nous sommes pour l'instant dans l'impossibilité d'accéder à votre demande.<br/>
	Nous sommes actuellement en train de trouver une solution et vous prions de bien vouloir patienter.
</p>
<p>
	Merci pour votre compréhension.
</p>
EOF
);

			$page->render();
		} catch (Exception $e) {
			include "../vues/erreur.html";
		}
	}
}
