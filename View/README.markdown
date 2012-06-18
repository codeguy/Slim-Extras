# Custom Views

The Slim Framework provides a default View class that uses PHP template files by default. This folder includes custom View classes that you may use with alternative template libraries, such as [Twig](http://www.twig-project.org/), [Smarty](http://www.smarty.net/), or [Mustache](http://mustache.github.com/).

## View_Twig

The `View_Twig` custom View class provides support for the [Twig](http://twig.sensiolabs.org/) template library. You can use the TwigView custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'views/Twig.php';

	$view = new View_Twig();
	$app = new Slim(array(
		'view' => $view
	));
	//Insert your application routes here
	$app->run();
	?>

You will need to configure the `$view->twigOptions` and `$view->twigDirectory` class variables before using the View_Twig class in your application. These variables can be found at the top of the `view/Twig.php` class definition.

## View_Mustache

The `View_Mustache` custom View class provides support for the [Mustache template language](http://mustache.github.com/) and the [Mustache.php library](github.com/bobthecow/mustache.php). You can use the MustacheView custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'view/MustacheView.php';

	$view = new View_Mustache();
	$view->mustacheDirectory = 'path/to/mustacheDirectory/';
	$app = new Slim(array(
		'view' => $view
	));
	//Insert your application routes here
	$app->run();
	?>

Before you can use the View_Mustache class, you will need to set `$view->mustacheDirectory`. This property should be the relative or absolute path to the directory containing the `Mustache.php` library.

## View_Smarty

The `View_Smarty` custom View class provides support for the [Smarty](http://www.smarty.net/) template library. You can use the View_Smarty custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'view/Smarty.php';

	$view = new View_Smarty();
	$app = new Slim(array(
		'view' => $view
	));
	//Insert your application routes here
	$app->run();
	?>

You will need to configure the `$view->smartyDirectory`,  `$view->smartyCompileDirectory` , `$view->smartyCacheDirectory` and optionally `$view->smartyTemplatesDirectory`, class variables before using the SmartyView class in your application. These variables can be found at the top of the `views/Smarty.php` class definition.

## View_Blitz

The `View_Blitz` custom View class provides support for the Blitz templating system for PHP. Blitz is written as C and compiled to a PHP extension. Which means it is FAST. You can learn more about Blitz at <http://alexeyrybak.com/blitz/blitz_en.html>. You can use the View_Blitz custom View in your Slim application like this:

	<?php
	require 'slim/Slim.php';
	require 'view/Blitz.php';

	$view = new View_Blitz();
	$app = new Slim(array(
		'view' => $view
	));
	//Insert your application routes here
	$app->run();
	?>

Place your Blitz template files in the designated templates directory.

## View_Haanga

The `View_Haanga` custom View class provides support for the Haanga templating system for PHP. Refer to the `views/View_Haanga.php` file for further documentation.

    <?php
	require 'slim/Slim.php';
	require_once 'view/Haanga.php';

	$view = new View_Haanga('/path/to/Haanga/dir', '/path/to/templates/dir', '/path/to/compiled/dir');
	$app = new Slim(array(
        'view' => $view
    ));
	//Insert your application routes here
	$app->run();
	?>

## View_H2o

The `View_H2o` custom View class provides support for the [H2o templating system](http://www.h2o-template.org) for PHP. You can use the View_H2o custom View in your application like this:

    <?php
	require 'slim/Slim.php';
	require 'view/H2o.php';

	$view = new View_H2o();
	$view->h2o_directory = './h2o/';

	$app = new Slim(array(
		'view' => $veiw
	));
	// Insert your application routes here
	$app->run();
	?>

Refer to the `View/H2o.php` file for further documentation.

