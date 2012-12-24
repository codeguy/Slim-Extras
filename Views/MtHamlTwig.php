<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart
 * @link        http://www.slimframework.com
 * @copyright   2011 Josh Lockhart
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Slim\Extras\Views;

/**
 * MTHamlView
 *
 * The HamlView is a Custom View class that renders templates using the
 * HAML template language (http://haml-lang.com/) through the use of
 * MTHaml (https://github.com/arnaud-lb/MtHaml).
 *
 * There are two field that you, the developer, will need to change:
 * - hamlDirectory
 * - hamlCacheDirectory
 *
 * @package Slim
 * @author  Adrian Demleitner <http://ichbinadrian.ch/>
 */
class MtHamlTwig extends \Slim\View
{

  /**
	 * @var string The path to the directory containing the "MtHaml" folder without trailing slash.
	 */
	public static $mthamlDirectory = null;

	/**
	 * @var string The path to the templates folder WITH the trailing slash.
	 */
	public static $mthamlCacheDirectory = null;

	/**
	 * @var string The path to the directory containing the "MtHaml" folder without trailing slash.
	 */
	public static $twigDirectory = null;

	/**
	 * Renders a template using Haml.php.
	 *
	 * @see View::render()
	 * @throws RuntimeException If MtHaml lib directory does not exist.
	 * @throws RuntimeException If Twig lib directory does not exist.
	 * @param string $template The template name specified in Slim::render()
	 * @return string
	 */	
	public function render($template)
	{
		if ( !is_dir(self::$mthamlDirectory) ) {
		    throw new \RuntimeException('Cannot set the MtHaml lib directory : ' . self::$mthamlDirectory . '. Directory does not exist.');
		}
 		if ( !is_dir(self::$twigDirectory) ) {
		    throw new \RuntimeException('Cannot set the Twig lib directory : ' . self::$twigDirectory . '. Directory does not exist.');
		}
       	
		require_once self::$mthamlDirectory . '/MtHaml/Autoloader.php';
		require self::$twigDirectory . '/Twig/Autoloader.php';
    
		\MtHaml\Autoloader::register();
		$mthaml = new \MtHaml\Environment('twig', array('enable_escaper' => false));
		$twig_filesystem = new \Twig_Loader_Filesystem(array($this->getTemplatesDirectory()));
		$twig_loader = new \MtHaml\Support\Twig\Loader($mthaml, $twig_filesystem);

		$twig = new \Twig_Environment($twig_loader, array(
			//'cache' => self::$mthamlCacheDirectory
		));
		$twig->addExtension(new \MtHaml\Support\Twig\Extension());
		echo $twig->render($template);
	}
}

?>
