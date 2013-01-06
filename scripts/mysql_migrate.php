<?php

include_once "../environment.php";

$page = new Pager('MySQL');
$page->headerTitle('MySQL tools');
$page->pageTitle('Outil de migration de base de données');

?>

<p>	-- Début de la migration -- </p>

<!-- Insertion des commandes pour la migration -->

<!-- 20130105 -->
<?php $DB->querySQL("CREATE TABLE  mariage.logs ("
	."id INT NOT NULL AUTO_INCREMENT, "
	."log_time DATETIME NOT NULL, "
	."message TEXT NOT NULL, "
	."PRIMARY KEY (  id )"
	.") ENGINE = MYISAM ;"
); ?>
<p> Création de la table de logs : [OK] </p>

<!-- Fin des commandes -->

<p>	-- Migration terminée -- </p>

<?php
$page->render('layout_entree.php');
?>