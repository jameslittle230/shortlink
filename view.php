<?php
require_once("secret.php");

if($_POST['password'] && $_POST['delete']) {
	$password = hash("sha256", $_POST['password']);

	$flag = false;
	foreach($passwords as $k => $v) {
		if($v == '*' && hash("sha256", $k) == $password) {
			$flag = true;
		}
	}

	if($flag) {
		$filename = __dir__ . "/bookmarks/" . $namespace . ".txt";
		$file = fopen($filename, 'r');
		$contents = file_get_contents($filename);

		while(!feof($file)) {
			$line = fgets($file);
			if($line == "") continue;
			$data = explode("\t", $line);
			if(in_array($data[0], $_POST['delete'])) {
				$contents = str_replace($line, '', $contents);
			}
		}

		fclose($file);
		file_put_contents($filename, $contents);
	} else {
		$msg = "Password invalid: could not delete shortlinks.";
	}
} else if ($_POST) {
	$msg = "Please select shortlinks to delete, enter the master password, and click the button.";
}
?>

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

.c-input-field--fill {
	width: 100%;
}

.boilerform .c-input-field {
	font-family: monospace;
}

.c-form__row {
	margin-bottom: 1rem;
	clear: both;
}

.c-form__row:after {
	display: table;
	content: "";
	clear: both;
}

.message {
	font-weight: bold;
	padding: 12px;
	background: #ccc;
	word-break: break-all;
}

</style>

<title>View all shortlinks</title>
</head>

<body>
<div class="page-wrap">
<h2 class="subtitle">or <a href="/">go back</a></h2>
<h1>View all shortlinks</h1>

<?php if($msg): ?>
<p class="message"><?php echo $msg; ?></p>
<?php endif; ?>

<form action="" method="POST" class="boilerform">
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
	printf('<li><code><a href="%s">%s</a>', $data[1], $urlstring);
	printf('<input type="checkbox" name="delete[]" value="%s" />', $data[0]);
	printf('<br>%s</code></li>', $data[1]);
}

?>
</ul>

<div class="c-form__row">
<label for="password" class="c-label">Password</label>
<input type="password" name="password" id="password" class="c-input-field">
</div>

<div class="c-form__row">
<button type="submit" class="c-button">Delete Selected Shortlinks</button>
</div>

</form>
</div>
</body></html>
