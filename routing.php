<?php
// Use this with the built-in PHP server
// php -S 127.0.0.1:8080 routing.php

// Check for static files but exclude dot files
if (is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/.') === false) {
  return false;
}

// Or load our script
$_SERVER['SCRIPT_NAME'] = '/index.php';
require 'index.php';
