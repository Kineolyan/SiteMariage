<?php

include_once '../environment.php';
include_once 'pageList.php';

function registerItem($db, $page, $ajax) {
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
		$json = array();
		foreach ($pages as $page) {
			$jsonPage = registerItem($db, $page->file(), true);
			
			if (NULL!=$jsonPage) {
				$json[] = $jsonPage;
			}
		}
		
		return $json;
	}
	else {
		foreach ($pages as $page) {
			registerItem($db, $page->file(), false);
		}
	}
}

function registerComponents($db, $components, $ajax) {
	if ($ajax) {
		$json = array();
		foreach ($components as $component) {
			$jsonPage = registerItem($db, $component, true);
			
			if (NULL!=$jsonPage) {
				$json[] = $jsonPage;
			}
		}
		
		return $json;
	}
	else {
		foreach ($components as $component) {
			registerItem($db, $component, false);
		}
	}
}

if ($VARS->isAjaxRequest()) {
	$jsonPages = registerPages($DB, $pagesList, true);
	$jsonComposants = registerComponents($DB, $componentsList, true);
	
	echo empty($jsonPages) && empty($jsonComposants) ? 
		'{}': json_encode(array_merge($jsonPages, $jsonComposants));
}
else {
	registerPages($DB, $pagesList, false);
	registerComponents($DB, $componentsList, false);
}

?>
