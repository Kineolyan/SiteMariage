<?php 

include_once 'util/dba/dba.mysql.class.php';
include_once 'util/dba/dba.pdo.class.php';
include_once 'util/visitor.class.php';
include_once 'util/helpers/pager.class.php';
include_once 'util/helpers/form.class.php';
include_once 'util/liste.class.php';
include_once 'util/vars.class.php';

session_start();

$usePDO = false;

// Creation de la base de données
if (isset($_GET['clear']) && 'session'==$_GET['clear']) {
	$DB = $usePDO? new DbaPdo(): new DbaMysql();
	$VISITOR = new Visitor($DB);
	
	$_SESSION['db'] = $DB;
	$_SESSION['visitor'] = $VISITOR;
}
else {
	
	if (!isset($_SESSION['db'])) {
		$_SESSION['db'] = $usePDO? new DbaPdo(): new DbaMysql();
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

$VARS = new Variables();
//var_dump($VARS);

?>