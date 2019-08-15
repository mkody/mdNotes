<?php
/* Look if we provide the filename */
$filename = ""; // Preventing notices
if(isset($_GET['f'])) {
	$filename = substr($_GET['f'], 1); // Get file name
	if(substr($filename, -1) == "/") $filename = substr($filename, 0, -1); // If there's a trailing slash
	if(substr($filename, -3) == ".md") $filename = substr($filename, 0, -3); // Remove extension
}

/* Init */
require('parsedown/Parsedown.php'); // Parsedown lib
require('parsedown-extra/ParsedownExtra.php'); // ParsedownExtra lib
require('ParsedownFilter/ParsedownFilter.php'); // ParsedownFilter lib
$parser = new ParsedownFilter('ourFilters');

/* Settings */
$parser->setBreaksEnabled(true);

function ourFilters(&$el) {
	switch($el['name']){
		case 'a':
			// Add rel and target to external links
			$url = $el['attributes']['href'];
			if(strpos($url, '://') === false) { // If it doesn't have a scheme...
				// Let's make sure it's not a "//" shortcut
				if((($url[0] == '/') && ($url[1] != '/')) || ($url[0] != '/')) {
					return;
				}
			}
			// If the link doesn't share the host, then it's external
			if(strpos($url, $_SERVER["SERVER_NAME"]) === false) {
				$el['attributes']['rel'] = 'noopener nofollow';
				$el['attributes']['target'] = '_blank';
			}
			break;
		case 'img':
			// Add lazy loading attribute to images
			$el['attributes']['loading'] = 'lazy';
			break;
		case 'h1':
		case 'h2':
		case 'h3':
		case 'h4':
		case 'h5':
		case 'h6':
			// Create a slug and set as id of headings
			$pattern = '/[^A-Za-z0-9àäéèêôõùüû\\-]/';
			$slug = $el['handler']['argument'];
			$slug = str_replace(' ', '-', $slug);
			$slug = preg_replace($pattern, '', $slug);
			// Remove trimming or double "-"
			$slug = preg_replace('/\-+/', '-', $slug);
			if (substr($slug, -1) == '-') $slug = substr($slug, 0, -1);
			$el['attributes']['id'] = $slug;
			break;
	}
}

/* Try to load document */
$dir = "data/"; // Where are the .md stored (can be relative or absolue path)
$markdown = @file_get_contents($dir . $filename . '.md'); // Read the file
$canDL = false; // Preventing notices messages from PHP

if(empty($filename)) { // This should be home, since no files are opened
	$markdown = "# Notes
Just a place for notes, _long messages_ and `code`.

Code in PHP, documents in Markdown.
[Get the code here](https://github.com/mkody/mdNotes)";
} else if(empty($markdown)) { // File is empty or doesn't exist
	header("HTTP/1.0 404 Not Found");
	$markdown = "# 404!
There's nothing here...";
} else { // Or the file is here, has content, and can be downloadable too
	$canDL = true;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Notes<?php if(!empty($filename)) echo " > ". $filename; ?></title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<style>
	body {margin: 0 15px 50px; cursor: default;}
	h2 {margin-bottom: 0; margin-left: -10px; padding: 10px;}
	h2:target {background-color: yellow;}
	a {cursor: pointer;}
	li {margin: 2px 0;}
	code {border: 1px solid #ccc;}
	pre > code {border: none;}
	td, th {padding: 0 5px;}
	del {background: black; color: black; text-decoration: none;}
	del:hover, del:active {color: white;}
	/* I don't like when images are too high */
	img {max-width: 100%; max-height: 400px; height: auto;}
	.dl-md {position: absolute; right: 5px; top: 0;}
</style>
</head>
<body>
<?php
// If the file exist, make it downloadable
if($canDL) echo '<a class="dl-md" href="/data/'. $filename .'.md">Download .md</a>';

// Render Markdown, new lines are converted to <br>
echo $parser->text($markdown);
?>

<!-- Using MKody's spaghetti code
     https://github.com/MKody/mdNotes -->
</body>
</html>
