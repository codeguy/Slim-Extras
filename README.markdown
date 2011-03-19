# Slim Framework for PHP 5 - Extras

This repository contains supplemental files for the Slim Framework, such as custom views and plugins. I created this repository to keep the primary Slim Framework repository as light-weight as possible.

## Custom Views

This repository contains custom View classes for the template frameworks listed below. To use any of these custom View classes, `require` the appropriate class in your Slim Framework bootstrap file and initialize your Slim application using an instance of the selected View class (see example below).

* Smarty
* Twig
* Mustache
* Haml
* Haanga
* Blitz
* Dwoo
* Sugar
* Savant
* Rain

To learn how to write your own custom View class, visit the [Slim Framework documentation](https://github.com/codeguy/Slim/wiki/Slim-Framework-Documentation#custom-views).

### How to use a custom View

    <?php
    //Require the Slim Framework
    require_once 'Slim/Slim.php';

    //Require the custom View
    require_once 'SmartyView.php';

    //Init Slim app with the custom View
    Slim::init(array(
        'view' => new SmartyView();
    ));

    //Implement the rest of your application
    //...
    ?>

## Plugins

Coming soon...