<?php

include_once "environment.php";

if ($VISITOR->hasAccess("Listing")) {
?>

<html>
<head>
	<title>Listing des inscrits</title>
</head>
<body>
	<h1>Listing</h1>
</body>
</html>

<?php }
else {
	include_once "include/acces.php";
}

?>