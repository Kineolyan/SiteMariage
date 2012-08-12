<?php

class Admin {
	public static function gererSoumission() {
		global $VARS, $DB;

		$messages = '';
		switch ($VARS->post('action')) {
			case 'update':
				$id = $VARS->post('userId', 'int');
				$access = $VARS->post('userAccess');
				$adminStatus = $VARS->post('admin');
				$DB->update('users', array(
						'allowed_pages' => $access[0],
						'admin' => $adminStatus[0]
				),
						"id=$id");
				break;

			case 'updatePassword':
				$password1 = $VARS->post('password1', 'string');
				$password2 = $VARS->post('password2', 'string');
				$userId = $VARS->post('login', 'int');
				if ($password1 == $password2 && 0 != $userId) {
					$DB->update('users',
							array('password' => Visitor::cryptPassword($password1)),
							"id=$userId");
					$messages.= "<p>Nouveau mot de passe pour $userId: $password1 [ok]</p>";
				}
				else {
				}
				break;

			case 'ajouterCategorie':
				$categorie = $VARS->post('categorie', 'string');
				if (-1 == Admin::ajouterCategorie($categorie)) {
					$VARS->setFlash('erreur', 'Impossible d\'ajouter la catégorie '.$categorie);
				}
				break;
		}

		$VARS->setFlash('message', $messages);
	}

	public static function gererAjax() {
		switch ($VARS->ajax('action')) {
			case 'ajouterCategorie':
				$categorie = $VARS->post('categorie', 'string');
				if (-1 == Admin::ajouterCategorie($categorie)) {
					return '{"erreur": "Impossible d\'ajouter la catégorie"}';
				} else {
					return "{\"succes\": \"$categorie\"}";
				}
		}
	}

	public static function ajouterCategorie($categorie) {
		global $DB;

		if ('' != $categorie) {
			return $DB->insert('categories', array('categorie' => $categorie));
		}

		return -1;
	}
}
