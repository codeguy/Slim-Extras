<?php
/**
 * LESS CSS Middleware
 *
 * Use this middleware with your Slim Framework application
 * to compile LESS CSS files to plain CSS on-the-fly.
 *
 * @author Gerard Sychay <hellogerard@gmail.com>
 * @version 1.0
 *
 * USAGE
 *
 * $app = new \Slim\Slim();
 * $app->add(new \Slim\Extras\Middleware\Less(array(
 *  'src' => '/path/to/public'
 * ));
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
namespace Slim\Extras\Middleware;

use \Slim\Middleware;
use \Assetic\Filter\LessphpFilter;
use \Assetic\Filter\CssMinFilter;
use \Assetic\Filter\CssImportFilter;
use \Assetic\Asset\FileAsset;
use \Assetic\Asset\AssetCache;
use \Assetic\Cache\FilesystemCache;

class Less extends Middleware
{
    /**
     * @var array
     */
    public $options;

    /**
     * Constructor
     *
     * @param   array  $options   Configuration options for LESS compilation
     */
    public function __construct($options = null)
    {
        // Default options
        $this->options = array(
            'src' => null,
            'cache' => true,
            'cache.dir' => null,
            'minify' => true,
            'debug' => false
        );

        if ($options)
        {
            $this->options = array_merge($this->options, (array) $options);
        }

        if ($this->options['src'] === null)
        {
            throw new \InvalidArgumentException("'src' directory is required.");
        }

        if ($this->options['cache.dir'] === null)
        {
            $this->options['cache.dir'] = $this->options['src'];
        }
    }

    /**
     * Debug
     *
     * This method will send messages to the Slim Logger at a DEBUG level.
     */
    public function debug($arg)
    {
        if ($this->options['debug'] === true)
        {
            $this->app->getLog()->debug($arg);
        }
    }

    /**
     * Call
     *
     * This method will check the HTTP request to see if it is a CSS file. If
     * so, it will attempt to find a corresponding LESS file. If one is found,
     * it will compile the file to CSS and serve it, optionally saving the CSS
     * to a filesystem cache. The request will end at this point.
     *
     * If the request is not for a CSS file, or if the corresponding LESS file
     * is not found, this middleware will pass the request on.
     */
    public function call()
    {
        $app = $this->app;

        // PHP 5.3 closures do not have access to $this, so we must proxy it in.
        // However, the proxy only has access to public fields.
        $self = $this;

        $app->hook('slim.before', function() use ($app, $self) {
            $path = $app->request()->getPathInfo();

            // Run filter only for requests for CSS files
            if (preg_match('/\.css$/', $path))
            {
                $path = preg_replace('/\.css$/', '.less', $path);

                // Get absolute physical path on filesystem for LESS file
                $abs = $self->options['src'] . '/' . $path;

                $self->debug("Looking for LESS file: $abs");

                $abs = realpath($abs);

                if ($abs === false)
                {
                    // If LESS file is not found, just return
                    $self->debug("LESS file not found: $abs");
                    return;
                }

                $self->debug("LESS file found: $abs");

                // Prepare Assetic
                $lessFilter = new LessphpFilter();
                $importFilter = new CssImportFilter();
                $css = new FileAsset($abs, array($lessFilter, $importFilter));

                if ($self->options['minify'] === true)
                {
                    // Minify, if desired
                    $self->debug("Minifying LESS file: $abs");
                    $css->ensureFilter(new CssMinFilter());
                }

                if ($self->options['cache'] === true)
                {
                    // Cache results, if desired
                    $self->debug("Caching LESS file: {$self->options['cache.dir']}");
                    $cache = new FilesystemCache($self->options['cache.dir']);
                    $css = new AssetCache($css, $cache);
                }

                // Render results and exit
                header('Content-Type: text/css');
                echo $css->dump();
                ob_end_flush();
                exit;
            }
        });

        $this->next->call();
    }
}
