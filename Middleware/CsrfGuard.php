<?php
/**
 * CSRF Guard
 *
 * Use this middleware with your Slim Framework application
 * to protect you from CSRF attacks.
 *
 * USAGE
 *
 * $app = new \Slim\Slim();
 * $app->add(new \Slim\Extras\Middleware\CsrfGuard());
 *
 */
namespace Slim\Extras\Middleware;

class CsrfGuard extends \Slim\Middleware
{
    /**
     * CSRF token key name.
     *
     * @var string
     */
    protected $key;

    /**
     * CSRF token header name.
     *
     * @var string
     */
    protected $header;

    /**
     * Constructor.
     *
     * @param string    $key        The CSRF token key name.
     * @param string    $header     The CSRF token header name.
     * @return void
     */
    public function __construct($key = 'csrf_token', $header = 'X-CSRFToken')
    {
        if (! is_string($key) || empty($key) || preg_match('/[^a-zA-Z0-9\-\_]/', $key)) {
            throw new \OutOfBoundsException('Invalid CSRF token key "' . $key . '"');
        }
        if (! is_string($header) || empty($header) || preg_match('/[^a-zA-Z0-9\-\_]/', $header)) {
            throw new \OutOfBoundsException('Invalid CSRF token header "' . $header . '"');
        }

        $this->key    = $key;
        $this->header = $header;
    }

    /**
     * Call middleware.
     *
     * @return void
     */
    public function call() 
    {
        // Attach as hook.
        $this->app->hook('slim.before', array($this, 'check'));

        // Call next middleware.
        $this->next->call();
    }

    /**
     * Check CSRF token is valid.
     * Note: Also checks POST data to see if a Moneris RVAR CSRF token exists.
     *
     * @return void
     */
    public function check() {
        // Check sessions are enabled.
        if (session_id() === '') {
            throw new \Exception('Sessions are required to use the CSRF Guard middleware.');
        }

        if (! isset($_SESSION[$this->key])) {
            $_SESSION[$this->key] = sha1(serialize($_SERVER) . rand(0, 0xffffffff));
        }

        $token = $_SESSION[$this->key];

        // Validate the CSRF token.
        if (in_array($this->app->request()->getMethod(), array('POST', 'PUT', 'DELETE'))) {
            $normalized_header = str_replace('-', '_', strtoupper($this->header));
            $userToken = $this->app->request()->post($this->key) ?: $this->app->environment()[$normalized_header];
            if ($token !== $userToken) {
                $this->app->halt(400, 'Invalid or missing CSRF token.');
            }
        }

        // Assign CSRF token key and value to view.
        $this->app->view()->appendData(array(
            'csrf_key'    => $this->key,
            'csrf_token'  => $token,
            'csrf_header' => $this->header
        ));
    }
}
