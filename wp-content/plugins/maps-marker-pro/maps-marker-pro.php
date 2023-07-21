<?php
/*
+----+-----+-----+-----+-----+----+-----+-----+-----+-----+-----+-----+
|          . _..::__:  ,-"-"._       |7       ,     _,.__             |
|  _.___ _ _<_>`!(._`.`-.    /        _._     `_ ,_/  '  '-._.---.-.__|
|.{     " " `-==,',._\{  \  / {)     / _ ">_,-' `                mt-2_|
+ \_.:--.       `._ )`^-. "'      , [_/(                       __,/-' +
|'"'     \         "    _L       oD_,--'                )     /. (|   |
|         |           ,'         _)_.\\._<> 6              _,' /  '   |
|         `.         /          [_/_'` `"(                <'}  )      |
+          \\    .-. )          /   `-'"..' `:._          _)  '       +
|   `        \  (  `(          /         `:\  > \  ,-^.  /' '         |
|             `._,   ""        |           \`'   \|   ?_)  {\         |
|                `=.---.       `._._       ,'     "`  |' ,- '.        |
+                  |    `-._        |     /          `:`<_|h--._      +
|                  (        >       .     | ,          `=.__.`-'\     |
|                   `.     /        |     |{|              ,-.,\     .|
|                    |   ,'          \   / `'            ,"     \     |
+                    |  /             |_'                |  __  /     +
|                    | |                                 '-'  `-'   \.|
|                    |/               Maps Marker Pro               / |
|                    \.    The most comprehensive & easy-to-use     ' |
+                                maps plugin for WordPress            +
|                     ,/           ______._.--._ _..---.---------._   |
|    ,-----"-..?----_/ )      _,-'"             "                  (  |
|.._(                  `-----'                                      `-|
+----+-----+-----+-----+-----+----+-----+-----+-----+-----+-----+-----+
ASCII Map (C) 1998 Matthew Thomas (freely usable as long as this line is included)

Plugin Name: Maps Marker Pro &reg;
Plugin URI: https://www.mapsmarker.com
Description: The most comprehensive & easy-to-use maps plugin for WordPress

Author: MapsMarker.com e.U.
Author URI: https://www.mapsmarker.com

Version: 4.24.1
Tested up to: 6.1.1
Requires at least: 4.5
Requires PHP: 5.6

Text Domain: mmp
Domain Path: /languages

License: All rights reserved
License URI: https://www.mapsmarker.com/tos/
Privacy Policy: https://www.mapsmarker.com/privacy/
Newsletter: https://www.mapsmarker.com/newsletter

Copyright 2011-2023 - MapsMarker.com e.U., MapsMarker &reg;
*/

if (!defined('ABSPATH')) {
	die;
}

spl_autoload_register(function($class) {
	$prefix = 'MMP\\';
	$base_dir = __DIR__ . '/classes/';
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}
	$relative_class = strtolower(substr($class, $len));
	$file = $base_dir . str_replace(array('_', '\\'), array('-', '/'), $relative_class) . '.php';
	if (file_exists($file)) {
		require $file;
	}
});

(new MMP\Maps_Marker_Pro(__FILE__))->init();
