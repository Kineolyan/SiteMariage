<?php

class Pager {
	static public function generateHeaders($pageTitle) {
		echo <<<HEADERS
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>$pageTitle</title>
</head>
<body>		
HEADERS;
	}
	
	static public function generateFooter() {
		echo <<<FOOTER
</body>
</html>
FOOTER;
	}
}
