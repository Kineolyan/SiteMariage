<?php

include_once 'scripts/config.php';
include_once 'util/dba/dba.mysql.class.php';
include_once 'util/dba/dba.pdo.class.php';
include_once 'util/visitor.class.php';
include_once 'util/helpers/pager.class.php';
include_once 'util/helpers/form.class.php';
include_once 'util/liste.class.php';
include_once 'util/vars.class.php';
include_once 'util/admin.class.php';
include_once 'util/models/categories.class.php';
include_once 'util/utils.class.php';
include_once 'util/helpers/html.helper.php';
include_once 'util/logger.class.php';

session_start();

set_exception_handler(array('Pager', 'handleException'));

// Creation de la base de données
if (isset($_GET['clear']) && 'session'==$_GET['clear']) {
	$DB = creerDb();
	$VISITOR = new Visitor($DB);

	$_SESSION['db'] = $DB;
	$_SESSION['visitor'] = $VISITOR;
}
else {

	if (!isset($_SESSION['db'])) {
		$_SESSION['db'] = creerDb();
	}
	$DB = $_SESSION['db'];

	if (!isset($_SESSION['visitor'])) {
		$_SESSION['visitor'] = new Visitor($DB);
		$VISITOR = $_SESSION['visitor'];
	}
	else {
		$VISITOR = $_SESSION['visitor'];
		$VISITOR->db($DB);
	}
}

Categories::init($DB);
$VARS = new Variables();
$LOGGER = new Logger();

?>