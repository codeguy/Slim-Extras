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
 * HamlView
 *
 * The HamlView is a Custom View class that renders templates using the
 * HAML template language (http://haml-lang.com/) through the use of
 * HamlPHP (https://github.com/sniemela/HamlPHP).
 *
 * There are three field that you, the developer, will need to change:
 * - parserDirectory
 * - parserCacheDirectory
 *
 * @package Slim
 * @author  Matthew Callis <http://superfamicom.org/>
 */
class Haml extends \Slim\View
{
	/**
	 * @var string The path to the directory containing the "HamlPHP" folder without trailing slash.
	 */
	public $parserDirectory = null;

	/**
	 * @var string The path to the templates folder WITH the trailing slash
	 */
	public $parserCacheDirectory = null;

	/**
     * @var parserInstance for rendering templates.
     */
	private $parserInstance = null;

	/**
	 * Renders a template using Haml.php.
	 *
	 * @see View::render()
     * @throws RuntimeException If Haml lib directory does not exist.
	 * @param string $template The template name specified in Slim::render()
	 * @return string
	 */
	public function render($template)
	{
        $parser = $this->getInstance();
		$file = $parser->parseFile($this->getTemplatesDirectory() . $template);
		return $parser->evaluate($file, $this->all());
	}

	/**
	 * Creates new Rain instance if it doesn't already exist, and returns it.
	 *
     * @throws \RuntimeException If Rain lib directory does not exist.
	 * @return \HamlPHP Instance
	 */
	public function getInstance()
	{
		if (! $this->parserInstance) {
            if (!is_dir($this->parserDirectory)) {
	            throw new \RuntimeException('Cannot set the HamlPHP lib directory : ' . $this->parserDirectory . '. Directory does not exist.');
	        }
			require_once $this->parserDirectory . '/HamlPHP/HamlPHP.php';
			require_once $this->parserDirectory . '/HamlPHP/Storage/FileStorage.php';
			$this->parserInstnce = new \HamlPHP(new \FileStorage($this->parserCacheDirectory));
		}

		return $this->parserInstance;
	}
}
