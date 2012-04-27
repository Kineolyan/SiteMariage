<?php

include_once 'util/dba.mysql.php';

try {
	$db = new DbaMysql('sql.free.fr', 'mariage', 'mo.peyrusse', 'magicien');
	echo 'Connexion reussie a la base de donnees.<br/>';
}
catch (Exception $e) {
	echo '[Erreur] ', $e->getMessage();
}

?>