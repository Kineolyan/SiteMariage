<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>Test de base de données</title>
</head>
<body>	

<?php

include_once 'util/dba/dba.mysql.php';

try {
	$db = new DbaMysql('localhost', 'mariage', 'root', '');
	//$db = new DbaMysql('sql.free.fr', 'mariage', 'mo.peyrusse', 'magicien');
	echo 'Connexion reussie a la base de donnees.<br/>';
}
catch (Exception $e) {
	echo 'Erreur >> ', $e->getMessage();
}

?>

</body>
</html>