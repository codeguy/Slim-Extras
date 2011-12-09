# Custom Views

The Slim Framework provides a default View class that uses PHP template files by default. This folder includes custom View classes that you may use with alternative template libraries, such as [Twig](http://www.twig-project.org/), [Smarty](http://www.smarty.net/), or [Mustache](http://mustache.github.com/).

## TwigView

The `TwigView` custom View class provides support for the [Twig](http://twig.sensiolabs.org/) template library. You can use the TwigView custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'views/TwigView.php';
	$app = new Slim(array(
		'view' => 'TwigView'
	));
	//Insert your application routes here
	$app->run();
	?>

You will need to configure the `TwigView::$twigOptions` and `TwigView::$twigDirectory` class variables before using the TwigView class in your application. These variables can be found at the top of the `views/TwigView.php` class definition.

## MustacheView

The `MustacheView` custom View class provides support for the [Mustache template language](http://mustache.github.com/) and the [Mustache.php library](github.com/bobthecow/mustache.php). You can use the MustacheView custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'views/MustacheView.php';
	MustacheView::$mustacheDirectory = 'path/to/mustacheDirectory/';
	$app = new Slim(array(
		'view' => 'MustacheView'
	));
	//Insert your application routes here
	$app->run();
	?>

Before you can use the MustacheView class, you will need to set `MustacheView::$mustacheDirectory`. This property should be the relative or absolute path to the directory containing the `Mustache.php` library.

## SmartyView

The `SmartyView` custom View class provides support for the [Smarty](http://www.smarty.net/) template library. You can use the SmartyView custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'views/SmartyView.php';
	$app = new Slim(array(
		'view' => 'SmartyView'
	));
	//Insert your application routes here
	$app->run();
	?>

You will need to configure the `SmartyView::$smartyDirectory`,  `SmartyView::$smartyCompileDirectory` , `SmartyView::$smartyCacheDirectory` and optionally `SmartyView::$smartyTemplatesDirectory`, class variables before using the SmartyView class in your application. These variables can be found at the top of the `views/SmartyView.php` class definition.

## BlitzView

The `BlitzView` custom View class provides support for the Blitz templating system for PHP. Blitz is written as C and compiled to a PHP extension. Which means it is FAST. You can learn more about Blitz at <http://alexeyrybak.com/blitz/blitz_en.html>. You can use the BlitzView custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'views/BlitzView.php';
	$app = new Slim(array(
		'view' => 'BlitzView'
	));
	//Insert your application routes here
	$app->run();
	?>

Place your Blitz template files in the designated templates directory.

## HaangaView

The `HaangaView` custom View class provides support for the Haanga templating system for PHP. Refer to the `views/HaangaView.php` file for further documentation.

    <?php
	require 'slim/Slim.php';
	require_once 'views/HaangaView.php';
	$app = new Slim(array(
        'view' => new HaangaView('/path/to/Haanga/dir', '/path/to/templates/dir', '/path/to/compiled/dir')
    ));
	//Insert your application routes here
	$app->run();
	?>
