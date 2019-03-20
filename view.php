<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="stylesheet" media="all" href="https://rawgit.com/hankchizljaw/boilerform/master/dist/css/boilerform.min.css" />
<link rel="stylesheet" href="https://rsms.me/inter/inter-ui.css" />

<style>
body {
	font-size: 16px;
	line-height: 1.3;
	font-family: "Inter UI", sans-serif;
	/*font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
		Helvetica, Arial, sans-serif, "Apple Color Emoji",
		"Segoe UI Emoji", "Segoe UI Symbol";*/
}

.page-wrap {
	width: 90%;
	max-width: 800px;
	margin: 0 auto;
	padding-top: 1rem;
}

.subtitle {
	text-transform: uppercase;
	margin-bottom: 0;
	font-size: 0.8rem;
	letter-spacing: 2px;
}

.subtitle + h1 {
	margin-top: 0;
}

li {
    margin-bottom: 1em;
}
</style>

<title>View all shortlinks</title>
</head>

<body>
<div class="page-wrap">
<h2 class="subtitle">or <a href="/">go back</a></h2>
<h1>View all shortlinks</h1>

<ul>
<?php

$namespace = "default";
$filename = __dir__ . "/bookmarks/" . $namespace . ".txt";
$file = fopen($filename, "r");

while(!feof($file)) {
	$line = fgets($file);
	if(trim($line) == "") {
		continue;
	}

	$data = explode("\t", $line);
    $urlstring = "jil.im/" . ($namespace != "default" ? ($namespace . '/') : "") . $data[0];
    echo "<li><code><a href='" . $data[1] . "'>" . $urlstring . "</a> <a href=\"/view?delete=$data[0]\" style=\"color: red !important;\">x</a><br>" . $data[1] . "</code></li>";
}

?>
</ul>
</div>
</body></html>
