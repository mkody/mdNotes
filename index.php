<?php

/* Look if we provide the filename */
$req = $_GET['f'] ?? '';
if (
  empty($req) && // If we didn't have anything in ?f=
  !str_starts_with($_SERVER['REQUEST_URI'], '/index.php') // And it's not a call to index.php
) {
  // Use the subpath
  $req = $_SERVER['REQUEST_URI'];
}

$filename = '';
if (!empty($req)) {
  $filename = ltrim($req, '/'); // Get file name
  if (substr($filename, -1) === '/') $filename = substr($filename, 0, -1); // If there's a trailing slash
  if (substr($filename, -3) === '.md') $filename = substr($filename, 0, -3); // Remove extension
}

/* Load dependencies */
require('lib/Parsedown.php');
require('lib/ParsedownExtra.php');
require('lib/ParsedownFilter.php');
$parser = new ParsedownFilter('ourFilters');

/* Settings */
$parser->setBreaksEnabled(true); // Use <br /> on new lines

// This function is the filter called in PardownFilter so we can add attributes
function ourFilters(&$el)
{
  switch ($el['name']) {
    case 'a':
      // Add rel and target to external links
      $url = $el['attributes']['href'];
      if (strpos($url, '://') === false) { // If it doesn't have a scheme...
        // Let's make sure it's not a "//" shortcut
        if ((($url[0] === '/') && ($url[1] !== '/')) || ($url[0] !== '/')) {
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
      // Make it lowercase and set it
      $slug = strtolower($slug);
      $el['attributes']['id'] = $slug;
      break;
  }
}

/* Try to load document */
$dir = __DIR__ . '/data/'; // Where the .md are stored
$markdown = @file_get_contents($dir . $filename . '.md'); // Read the file

if (empty($filename)) { // Home page, since no document was asked
  $markdown = '# Notes
Just a place for notes, _long messages_ and `code`.';
} else if (empty($markdown)) { // File is empty or doesn't exist
  header('HTTP/1.0 404 Not Found');
  $markdown = '# 404!
There\'s nothing here...';
}

// Parse our markdown
$output = $parser->text($markdown);

// Check if we have codeblocks to load highlight.js
$hasCodeblock = strpos($output, '<code class="language-') !== false;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notes<?php if (!empty($filename)) echo ' > ' . $filename; ?></title>
  <link href="/assets/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 15px;
      cursor: default;
    }

    h2:target,
    h3:target,
    h4:target,
    h5:target,
    h6:target {
      background-color: yellow;
    }

    a {
      cursor: pointer;
    }

    code {
      border: 1px solid #ccc;
    }

    pre>code {
      border: none;
    }

    del {
      background: black;
      color: black;
      text-decoration: none;
    }

    del:hover,
    del:active {
      color: white;
    }

    /* I don't like when images are too high */
    img {
      max-width: 100%;
      max-height: 400px;
      height: auto;
    }
  </style>
<?php if ($hasCodeblock) { ?>
  <link rel="stylesheet" href="/assets/github.min.css">
  <script src="/assets/highlight.min.js"></script>
<?php } ?>
</head>
<body>
<?php
  echo $output . PHP_EOL;

  if ($hasCodeblock) {
?>
<script>
  hljs.configure({
    cssSelector: 'pre code[class^="language-"]'
  })
  hljs.highlightAll()
</script>
<?php } ?>
<!--
  Using MKody's spaghetti code
  https://git.rita.moe/kody/mdNotes
-->
</body>
</html>
