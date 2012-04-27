<?php 

include_once 'util/dba.php';
include_once 'util/visitor.php';
include_once 'util/pager.php';

session_start();

// Creation de la base de données
if (isset($_GET['clear']) && 'session'==$_GET['clear']) {
	$DB = new Dba();
	$VISITOR = new Visitor($DB);
	
	$_SESSION['db'] = $DB;
	$_SESSION['visitor'] = $VISITOR;
}
else {
	
	if (!isset($_SESSION['db'])) {
		$_SESSION['db'] = new Dba();
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

?>