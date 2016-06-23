<?php
$filename = ""; // Preventing notices
if(isset($_GET['f'])) {
	$filename = substr($_GET['f'], 1); // Get file name
	if(substr($filename, -1) == "/") $filename = substr($filename, 0, -1); // If there's a trailing slash
	if(substr($filename, -3) == ".md") $filename = substr($filename, 0, -3); // Remove extension
}

require('parsedown/Parsedown.php'); // Parsedown lib
require('parsedown-extra/ParsedownExtra.php'); // ParsedownExtra lib
$Extra = new ParsedownExtra(); // Create object

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
<title>Notes <?php if(!empty($filename)) echo " > ". $filename; ?></title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<style>
	body {margin: 0 15px;}
	/* I don't like when images are too high */
	img {max-width: 100%; max-height: 400px; height: auto;}
	.dl-md {position: absolute; right: 5px; top: 0;}
</style>
</head>
<body>
<?php
// If the file exist, make it downloadable
if($canDL) echo '<a class="dl-md" href="/data/'. $filename .'.md">Download Markdown</a>';

// Render Markdown, new lines are converted to <br>
echo $Extra->setBreaksEnabled(true)->text($markdown);
?>

<!-- Using MKody's spaghetti code
     https://github.com/MKody/mdNotes -->
</body>
</html>
