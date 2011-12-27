<?php

class Extension_Twig_Slim extends Twig_Extension
{
    public function getName()
    {
        return 'slim';
    }

    public function getFunctions()
    {
        return array(
            'urlFor' => new Twig_Function_Function('Slim::getInstance()->urlFor'),
        );
    }
}
