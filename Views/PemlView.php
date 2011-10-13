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

/**
 * PemlView
 *
 * The PemlView is a Custom View class that renders templates using the
 * Peml template language (http://code.google.com/p/peml/).
 * The PemlView is based on HamlView.
 *
 * There are three field that you, the developer, will need to change:
 * - pemlDirectory
 * - pemlTemplatesDirectory
 * - pemlCacheDirectory
 *
 * @package Slim
 * @author  Matias Russitto <http://russitto.com/>
 */

class PemlView extends Slim_View {

	/**
	 * @var string The path to the directory containing the "PemlPHP" folder without trailing slash.
	 */
	public static $pemlDirectory = null;

	/**
	 * @var string The path to the templates folder WITH the trailing slash
	 */
	public static $pemlTemplatesDirectory = 'templates/';

	/**
	 * @var string The path to the templates folder WITH the trailing slash
	 */
	public static $pemlCacheDirectory = null;


	/**
	 * Renders a template using Peml.php.
	 *
	 * @see View::render()
     * @throws RuntimeException If Peml lib directory does not exist.
	 * @param string $template The template name specified in Slim::render()
	 * @return string
	 */	
	public function render( $template ) {
        if ( !is_dir(self::$pemlDirectory) ) {
            throw new RuntimeException('Cannot set the Peml lib directory : ' . self::$pemlDirectory . '. Directory does not exist.');
        }

		require_once self::$pemlDirectory . '/peml/peml.php';

    $filepath = self::$pemlTemplatesDirectory.$template;
    $fileid = md5($filepath);
    $dir = self::$pemlCacheDirectory;
    if (!is_dir($dir))
      mkdir($dir);
    $storepath = $dir . DIRECTORY_SEPARATOR . $fileid . ".peml";
    
    if (pemlConfig::$settings['alwaysParse'] || !is_file($storepath) || filemtime($filepath) > filemtime($storepath)) {
      
      // Grab the file into an array and cut off the first line.
      // $peml_data = array_slice(preg_split("/\r?\n/", file_get_contents($filepath)), 1);
      $peml_data = file_get_contents($filepath);
      
      $peml = new pemlCore;
      $content = $peml->parse($peml_data);
      
      file_put_contents($storepath, $content);
    }
    require_once($storepath);
	}
}

?>
