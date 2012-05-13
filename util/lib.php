<?php

function autoload($class) {
	echo $class;
	require_once str_replace('\\', '/', strtolower($class)).'.class.php';
}

spl_autoload_register('autoload');