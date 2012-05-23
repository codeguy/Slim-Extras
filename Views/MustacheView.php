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
 * MustacheView
 *
 * The MustacheView is a Custom View class that renders templates using the
 * Mustache template language (http://mustache.github.com/) and the
 * [Mustache.php library](github.com/bobthecow/mustache.php).
 *
 * There is one field that you, the developer, will need to change:
 * - mustacheDirectory
 * Setting the field is not required if the Mustache class is found by the
 * autoloader.
 * 
 * @package Slim
 * @author  Johnson Page <http://johnsonpage.org>
 * @author  Michael Heim <http://www.zeilenwechsel.de>
 */
class MustacheView extends Slim_View {

    /**
     * @var string The path to the directory containing Mustache.php.
     *             Not required if Mustache.php is autoloaded.
     */
    public static $mustacheDirectory = null;

    /**
     * Append data to existing View data. Can handle arrays as well as an object (for PHP-5.2-compatible lambda
     * functions in Mustache).
     *
     * Note that you can append arrays to an existing object, or an object to an existing array, but NOT an object to
     * an object. Use one object only, at the most.
     *
     * @param   array|Object $data
     * @return  void
     */
    public function appendData( $data ) {
        if ( is_object($this->data) and is_object($data) ) {
            // Can't merge two objects safely, internal state might be lost
            throw new InvalidArgumentException("Can't merge view data of multiple objects");
        } elseif ( is_object($this->data) ) {
            foreach ( $data as $property => $item ) $this->data->$property = $item;
        } elseif ( is_object($data) ) {
            foreach ( $this->data as $property => $item ) $data->$property = $item;
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }
    }

    /**
     * Renders a template using Mustache.php.
     *
     * @see View::render()
     * @param string $template The template name specified in Slim::render()
     * @return string
     */
    public function render( $template ) {
        if (!is_null(self::$mustacheDirectory)) require_once self::$mustacheDirectory . '/Mustache.php';
        if ( class_exists( 'Mustache' ) ) {

            // Mustache 1.x
            $m = new Mustache();

            // Add support for the fixed-partials branch of Mustache 1.x
            if ( method_exists($m, "_setTemplateBase") ) $m->_setTemplateBase($this->getTemplatesDirectory());

            $contents = file_get_contents($this->getTemplatesDirectory() . '/' . ltrim($template, '/'));
            return $m->render($contents, $this->data);

        } else {

            // Mustache 2.x
            $loader = new Mustache_Loader_FilesystemLoader($this->getTemplatesDirectory());
            $m = new Mustache_Engine( array(
                'loader' => $loader,
                'partials_loader' => $loader
            ) );

            return $m->render(ltrim($template, '/'), $this->data);
        }
    }
}

?>