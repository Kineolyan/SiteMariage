<?php

function registerPage($db, $page, $ajax) {
	if ($ajax) {
		if (0==$db->count('pages', '*', "title='$page'")) {
			$id = $db->insert('pages', array('title' => $page));
			return json_encode(array(
				'id' => $id, 'title' => $page
			));
		}
		else {
			return NULL;
		}
	}
	else {
		echo 'enregistrement de '.$page;
		if (0==$db->count('pages', '*', "title='$page'")) {
			$db->insert("INSERT INTO pages(title) VALUES ('{$page}');");
			echo ' [OK]';
		}
		else {
			echo ' [done]';
		}
		echo '<br/>';
	}	
}

function registerPages($db, $pages, $ajax) {
	if ($ajax) {
		foreach ($pages as $page=>$contenu) {
			if (is_array($contenu)) {
				$jsonMessage = registerPage($db, $page, true);
				$json = registerPages($db, $contenu);
				$json[] = $jsonMessage;
				return $json;
			}
			else {
				return array(registerPage($db, $contenu, true));
			}
		}
	}
	else {
		foreach ($pages as $page=>$contenu) {
			if (is_array($contenu)) {
				registerPage($db, $page, false);
				registerPages($db, $contenu);
			}
			else {
				registerPage($db, $contenu, false);
			}
		}
	}
}

if ($VARS->isAjaxRequest()) {
	$jsonArray = registerPages($DB, $pagesList, true);
	echo empty($jsonArray)? '{}': json_encode($jsonArray);
}
else {
	registerPages($DB, $pagesList, false);
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