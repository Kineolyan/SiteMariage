<?php

include_once "../environment.php";

$pagesList = array(
		'Index',
		'Infos',
		'Listing',
		'Facture'
);

foreach ($pagesList as $title) {
	echo 'enregistrement de '.$title;
	$DB->querySQL("SELECT COUNT(*) FROM pages WHERE title='".$title."';");
	if (!$DB->fetch()) {
		$DB->querySQL("INSERT INTO pages(title) VALUES ('{$title}');");
		echo ' [OK]';
	}
	else {
		echo ' [done]';
	}
	echo '<br/>';
	$DB->endQuery();
}

// $inRegistrationProcess = true;

// function registerPage($path) {
// 	global $DB, $inRegistrationProcess;
// 	echo "registration of ".$path.'<br/>';
	
// 	include_once($path);
	
// 	if (0==$DB->querySQL("SELECT COUNT(*) FROM pages WHERE id='".$pageId."';", true)) {
// 		$DB->querySQL("INSERT INTO pages(id, title) VALUES ('{$pageId}', '{$pageTitle}');");
// 	}
// }

// function registerFolder($path) {
// 	echo $path.'<br/>';
// 	$exclusionList = array('..', '.', '.buildpath', '.settings',
// 		'.project', 'environment.php', 'include', 'pagesRegistration.php');
// 	$handle = opendir($path);
	
// 	while ($file = readdir($handle)) {
// 		if (!in_array($file, $exclusionList)) {
// 			if (is_dir($file)) {
// 				registerFolder($path.$file.'/');
// 			}
// 			else {
// 				registerPage($path.$file);
// 			}
// 		}
// 	}
// }

// registerFolder('../');

?>