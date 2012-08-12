<?php

function creerDb() {
	$USE_PDO = false;

	return $USE_PDO? new DbaPdo(): new DbaMysql();
}