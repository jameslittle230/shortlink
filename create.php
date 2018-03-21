<?php

require_once("secret.php");

function checkData($passwords) {
	if(isset($_POST['url'])) {
		$url = filter_var($_POST['url'], FILTER_VALIDATE_URL);
		if($url == "") {
			return "Please enter a valid URL.";
		}

		$short = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['short']);
		if($short == "") {
			return "Please enter a valid shortcode.";
		}

		$namespace = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['namespace']);
		$password = hash("sha256", $_POST['password']);

		foreach($passwords as $k => $v) {
			if(hash("sha256", $k) == $password) {
				if($v == '*') {
					return createShortlink($url, $short, $namespace);
				} else {
					if(in_array($namespace, $v)) {
						return createShortlink($url, $short, $namespace);
					}
				}
			}
		}

		return "Password invalid: could not create shortcode.";
	} else {
		return "";
	}
}

function createShortlink($url, $short, $namespace) {
	if($namespace == "default") {
		return "Cannot create shortcode: 'default' is a reserved word.";
	}

	if($namespace == "") {
		$namespace = "default";
	}

	$filename = __dir__ . "/bookmarks/" . $namespace . ".txt";

	if(!file_exists($file)) {
		$file = fopen($filename, 'w');
		fclose($file);
	}
	$file = fopen($filename, 'r');
	$contents = file_get_contents($filename);

	while(!feof($file)) {
		$line = fgets($file);
		if($line == "") {
			continue;
		}

		$data = explode("\t", $line);
		if($short == $data[0]) {
			$contents = str_replace(PHP_EOL . $line, '', $contents);
			file_put_contents($filename, $contents);
		}
	}

	fclose($file);
	$file = fopen($filename, 'a');
	fwrite($file, PHP_EOL . $short . "\t" . $url);
	fclose($file);

	$urlstring = "http://jil.im/" . ($namespace != "default" ? $namespace . '/' : "") . $short;
	return "New shortlink created: <a href='$urlstring'>$urlstring</a> goes to $url";
}

$msg = checkData($passwords);

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

.c-form__col {
	float: left;
	margin-right: 2rem;
}

.message {
	font-weight: bold;
	padding: 12px;
	background: #ccc;
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
</style>

<title>Create a shortlink</title>
</head>

<body>

<div class="page-wrap">

<?php if($msg): ?>
<p class="message"><?php echo $msg; ?></p>
<?php endif; ?>

<h2 class="subtitle">jil.im</h2>
<h1>Create a shortlink</h1>

<form action="/" method="POST" class="boilerform">

<div class="c-form__row">
<label for="url" class="c-label">URL</label>
<input type="text" name="url" id="url" placeholder="http://google.com/..." class="c-input-field c-input-field--fill">
</div>

<div class="c-form__row">
	<div class="c-form__col">
	<label for="short" class="c-label">Shortcode</label>
	<input type="text" name="short" id="short" class="c-input-field">
	</div>

	<div class="c-form__col">
	<label for="namespace" class="c-label">Namespace</label>
	<input type="text" name="namespace" id="namespace" placeholder="Blank is default" class="c-input-field">
	</div>
</div>

<div class="c-form__row">
<label for="short_options" class="c-label">Options</label>
<div class="c-check-field c-check-field--radio">
    <input type="radio" name="short_options" id="input" checked class="c-check-field__input" />
    <label for="input" class="c-check-field__decor" aria-hidden="true" role="presentation"></label>
    <label for="input" class="c-check-field__label">Input my own</label>
</div>

<div class="c-check-field c-check-field--radio">
    <input type="radio" name="short_options" id="random" class="c-check-field__input" />
    <label for="random" class="c-check-field__decor" aria-hidden="true" role="presentation"></label>
	<label for="random" class="c-check-field__label">Generate something random</label>
	<button style="margin-left: 1em;" id="randomize" disabled>Randomize...</button>
</div>
</div>

<div class="c-form__row">
<label for="short" class="c-label">Password</label>
<input type="password" name="password" id="password" class="c-input-field">
</div>

<div class="c-form__row">
<button type="submit" class="c-button">Submit</button>
</div>
</form>

</div>

<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>

<script>
function randomString() {
	var text = "";
	var possible = "abcdefghijkmnopqrstuvwxyz0123456789";

	for (var i = 0; i < 5; i++) {
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}

	return text;
}

$('input[type="radio"]').click(function() {
	if($('#random').is(':checked')) {
		$('#short').attr('disabled', 'disabled');
		$('#randomize').removeAttr('disabled');
		$('#short').val(randomString());
	} else {
		$('#short').removeAttr('disabled');
		$('#randomize').attr('disabled', 'disabled');
		$('#short').val("");
	}
});

$('#randomize').click(function(e) {
	e.preventDefault();
	if($('#random').is(':checked')) {
		$('#short').val(randomString());
	}
});
</script>

</body></html>
