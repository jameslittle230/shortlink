<?php

$uri = explode("/", $_SERVER["REQUEST_URI"]);
$path = preg_replace("/[^a-zA-Z0-9]+/", "", $uri[1]);
$namespace = "default";

/**
 * If we access http://jil.im/ then go to the creation interface
 */
if(count($uri) == 2 && $uri[1] == "") {
	include "create.php";
	exit;
}

/*
 * If there are more than 4 URI components (http://jil.im/a/b/c/something) then
 * go to the error page -- this is an invalid path
 */
if(count($uri) > 3) {
	include "error.php";
	exit;
}

/*
 * If there are more than 3, we are using a namespace: set the namespace and
 * path variables correctly
 */
if(count($uri) > 2) {
	$namespace = $path;
	$path = preg_replace("/[^a-zA-Z0-9]+/", "", $uri[2]);

	if($namespace == "default") {
		include "error.php";
		exit;
	}
}

/*
 * Open bookmarks file
 */
$filename = __dir__ . "/bookmarks/" . $namespace . ".txt";
$file = fopen($filename, "r");

/*
 * If file doesn't exist, go to error page
 */
if(!$file) {
	include "error.php";
	exit;
}

/*
 * Parse TSV bookmarks file and find data record with the path
 */
while(!feof($file)) {
	$line = fgets($file);
	if($line == "") {
		continue;
	}

	$data = explode("\t", $line);
	if($path == $data[0]) {
		/* Data record with path has been found */
		// $urlstring = "http://jil.im/" . ($namespace != "default" ? $namespace . '/' : "") . $path;
		shell_exec('curl -X POST --data-urlencode "payload={\"channel\": \"#debug\", \"username\": \"jil.im\", \"text\": \"thing visited!\", \"icon_emoji\": \":link:\"}" https://hooks.slack.com/services/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');
		header("Location: " . $data[1], true, 301);
		exit();
	}
}

include "error.php";

exit;
