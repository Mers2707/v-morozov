<?php
// show error alerts
error_reporting(E_ALL);
 
// set timezone 
date_default_timezone_set('Europe/Moscow');
 
// variable for JWT
$key = "itra";
$iss = "http://any-site.org";
$aud = "http://any-site.com";
$iat = 1356999524;
$nbf = 1357000000;
?>