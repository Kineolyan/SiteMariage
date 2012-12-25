<?php

class Admin {
	public static function gererSoumission() {
		global $VARS, $DB;

		switch ($VARS->post('action')) {
			case 'updateAccess':
				if ('' != $VARS->post('mettreAJour', 'string')) {
					$id = $VARS->post('userId', 'int');
					$access = $VARS->post('selectedPages');
					$adminStatus = $VARS->post('admin', 'int');
					$DB->update('users', array(
						'allowed_pages' => implode(',', $access), 'admin' => $adminStatus
					), "id=$id");

					$VARS->setFlash('editLogin', $VARS->post('editLogin'));
					$VARS->succes('Droits mis à jour');
					return true;
				}

				return false;

			case 'updatePassword':
				$password1 = $VARS->post('password1', 'string');
				$password2 = $VARS->post('password2', 'string');
				$login = $VARS->post('login', 'string');
				if ($password1 == $password2 && '' != $login) {
					$DB->update('users',
							array('password' => Visitor::cryptPassword($password1)),
							"login='$login'");
					$VARS->succes("Mot de passe mis à jour");
				}
				else {
					$VARS->erreur("Impossible de mettre à jour le mot de passe");
				}
				return true;

			case 'ajouterCategorie':
				$categorie = $VARS->post('categorie', 'string');
				if (-1 == Admin::ajouterCategorie($categorie)) {
					$VARS->erreur('Impossible d\'ajouter la catégorie '.$categorie);
				}
				return true;
		}
	}

	public static function gererAjax() {
		global $VARS;

		switch ($VARS->ajax('action')) {
		case 'ajouterCategorie':
			$categorie = $VARS->ajax('categorie', 'string');
			if (true -1 != Admin::ajouterCategorie($categorie)) {
				return "{\"success\": \"$categorie\"}";
			} else {
				return '{"erreur": "Impossible d\'ajouter la catégorie"}';
			}
			break;

		default:
			return '{}';
		}
	}

	public static function ajouterCategorie($categorie) {
		global $DB;

		if ('' != $categorie && 0 == $DB->count('categories', '*', "categorie='$categorie'")) {
			return $DB->insert('categories', array('categorie' => $categorie));
		}

		return -1;
	}
}
