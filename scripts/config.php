<?php

function creerDb() {
	$USE_PDO = false;

	return $USE_PDO? new DbaPdo(): new DbaMysql();
}

// Activate on production site
// define('PRODUCTION_SITE', '1');

?>