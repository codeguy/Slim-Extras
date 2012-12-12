<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author    Josh Lockhart
 * @link      http://www.slimframework.com
 * @copyright 2012 Josh Lockhart
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
 * LayoutView
 *
 * LayoutView is the custom View class that renders templates inside of layout.
 *
 * $content_for_layout variable contains rendered template output, which can be accessed inside the layout file.
 *
 * Example usage:
 * {{{
 * $app = new \Slim\Slim(array(
 *     'view' => new \Slim\Extras\Views\LayoutView(
 *         'layout.auto' => true,
 *         'layout.path' => './layouts',
 *         'layout.file' => 'layout.php'
 *     )
 * ));
 * }}}
 *
 * Sample layout file:
 * {{{
 * <!DOCTYPE html>
 * <html>
 * <body>
 * <p><?=$content_for_layout; ?></p>
 * </body>
 * </html>
 * }}}
 *
 * You may want to disable layouts for specific routes. Just call $app->view()->setAutoLayout(false) to disable rendering
 * templates inside of layout.
 *
 * @package Slim
 * @author  Hidayet Dogan
 * @link    http://hi.do 
 */
class LayoutView extends \Slim\View
{
    /**
     * @var boolean Turn on or off template renderding inside of layout. Default is true.
     */
    protected $autoLayout = true;

    /**
     * @var string Name of the layout file to render the template inside of. Default is layout.php.
     */
    protected $layoutFile = 'layout.php';

    /**
     * @var string Absolute or relative path to the application's layout directory.
     */
    protected $layoutDirectory;

    /**
     * Configure layout settings.
     * Available options are:
     * - layout.auto Toggle layout rendering.
     * - layout.file Name of the layout file relative to layout path.
     * - layout.path Layout directory.
     *
     * @param array $config Optional configuration settings for view.
     */
    public function __construct($config = array())
    {
        if (isset($config['layout.auto']))
        {
            $this->setAutoLayout($config['layout.auto']);
        }

        if (isset($config['layout.file']))
        {
            $this->setLayout($config['layout.file']);
        }

        if (isset($config['layout.path']))
        {
            $this->setLayoutDirectory($config['layout.path']);
        }
    }

    /**
     * Get auto layout rendering. Default is true.
     *
     * @return boolean
     */
    public function getAutoLayout()
    {
        return $this->autoLayout;
    }

    /**
     * Set auto layout rendering.
     *
     * @param boolean $autoLayout
     */
    public function setAutoLayout($autoLayout)
    {
        $this->autoLayout = $autoLayout;
    }

    /**
     * Get layout file. Default is layout.php.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layoutFile;
    }

    /**
     * Set layout file.
     *
     * @param string
     */
    public function setLayout($layout)
    {
        $this->layoutFile = ltrim($layout, '/');
    }

    /**
     * Get layout directory.
     *
     * @return string|null Path to layout directory. Returns null if layout directory is not set.
     */
    public function getLayoutDirectory()
    {
        return $this->layoutDirectory;
    }

    /**
     * Set layout directory.
     *
     * @param string $dir
     */
    public function setLayoutDirectory($dir)
    {
        $this->layoutDirectory = rtrim($dir, '/');
    }

    /**
     * Set templates directory. If the layout directory is not set before, templates directory is used.
     *
     * @see   View::setTemplatesDirectory()
     * @param string $dir
     */
    public function setTemplatesDirectory($dir)
    {
        if (empty($this->layoutDirectory))
        {
            $this->setLayoutDirectory($dir);
        }

        parent::setTemplatesDirectory($dir);
    }

    /**
     * Render template. If auto layout renderding is enabled, template is rendered inside of the layout.
     *
     * @see    View::render()
     * @param  string $template Name of the template file relative to templates directory.
     * @return string
     * @throws RuntimeException
     */
    public function render($template)
    {
        $this->setTemplate($template);
        extract($this->data);
        ob_start();
        require $this->templatePath;
        $content_for_layout = ob_get_clean();

        if ($this->autoLayout)
        {
            $layoutPath = $this->getLayoutDirectory() . '/' . $this->layoutFile;

            if (!file_exists($layoutPath))
            {
                throw new \RuntimeException('View cannot render layout `' . $layoutPath . '`. Layout does not exists.');
            }

            ob_start();
            require $layoutPath;
            return ob_get_clean();
        }
        else
        {
            return $content_for_layout;
        }
    }
}
