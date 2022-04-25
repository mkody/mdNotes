<?php
/* Look if we provide the filename */
$filename = '';
if (isset($_GET['f'])) {
	$filename = ltrim($_GET['f'], '/'); // Get file name
	if(substr($filename, -1) == '/') $filename = substr($filename, 0, -1); // If there's a trailing slash
	if(substr($filename, -3) == '.md') $filename = substr($filename, 0, -3); // Remove extension
}

/* Init */
require('parsedown/Parsedown.php'); // Parsedown lib
require('parsedown-extra/ParsedownExtra.php'); // ParsedownExtra lib
require('ParsedownFilter/ParsedownFilter.php'); // ParsedownFilter lib
$parser = new ParsedownFilter('ourFilters');

/* Settings */
$parser->setBreaksEnabled(true);

function ourFilters(&$el) {
	switch ($el['name']) {
		case 'a':
			// Add rel and target to external links
			$url = $el['attributes']['href'];
			if (strpos($url, '://') === false) { // If it doesn't have a scheme...
				// Let's make sure it's not a "//" shortcut
				if ((($url[0] == '/') && ($url[1] != '/')) || ($url[0] != '/')) {
					return;
				}
			}
			// If the link doesn't contain our current host, then it's external
			if (strpos($url, $_SERVER['SERVER_NAME']) === false) {
				$el['attributes']['rel'] = 'noopener nofollow';
				$el['attributes']['target'] = '_blank';
			}
			break;
		case 'img':
			// Add lazy loading attribute to images
			$el['attributes']['loading'] = 'lazy';
			break;
		case 'blockquote':
			// Add bootstrap class
			$el['attributes']['class'] = 'blockquote';
			break;
		case 'table':
			// Add bootstrap class
			$el['attributes']['class'] = 'table table-borderless table-hover table-sm';
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
			// Remove double and trailing "-"
			$slug = preg_replace('/\-+/', '-', $slug);
			$slug = rtrim($slug, '-');
			$el['attributes']['id'] = $slug;
			break;
	}
}

/* Try to load document */
$dir = __DIR__ . '/data/'; // Where are the .md stored (can be relative or absolue path)
$markdown = @file_get_contents($dir . $filename . '.md'); // Read the file

if (empty($filename)) { // This should be home, since no files are opened
	$markdown = '# Notes
Just a place for notes, _long messages_ and `code`.

Code in PHP, documents in Markdown.
[Get the code here](https://github.com/mkody/mdNotes)';
} else if (empty($markdown)) { // File is empty or doesn't exist
	header('HTTP/1.0 404 Not Found');
	$markdown = '# 404!
There\'s nothing here...';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Notes<?php if (!empty($filename)) echo ' > '. $filename; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.5.1/build/styles/github.min.css" integrity="sha256-Oppd74ucMR5a5Dq96FxjEzGF7tTw2fZ/6ksAqDCM8GY=" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.5.1/build/highlight.min.js" integrity="sha256-fTESf7xsfN/vHrWYAPnsUM7bFe+oH/Vx3PpdE6CtkPQ=" crossorigin="anonymous"></script>
<style>
	body {margin: 15px; cursor: default;}
	h2:target, h3:target, h4:target, h5:target, h6:target {background-color: yellow;}
	a {cursor: pointer;}
	code {border: 1px solid #ccc;}
	pre > code {border: none;}
	del {background: black; color: black; text-decoration: none;}
	del:hover, del:active {color: white;}
	/* I don't like when images are too high */
	img {max-width: 100%; max-height: 400px; height: auto;}
</style>
</head>
<body>
<?php
// Render Markdown, new lines are converted to <br>
echo $parser->text($markdown);
?>

<script>
hljs.configure({
	cssSelector: 'pre code[class^="language-"]'
})
hljs.highlightAll()
</script>
<!-- Using MKody's spaghetti code
     https://github.com/MKody/mdNotes -->
</body>
</html>
