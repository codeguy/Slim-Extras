<?php

/**
 * CsrfGuard
 *
 * This middleware provides protection from CSRF attacks

 * USAGE
 *
 * // Adding middleware
 * $app = new Slim();
 * $app->add(new CsrfGuard());
 *
 * // Setting token in view
 * <input type="hidden" name="<?=$csrf_key?>" value="<?=$csrf_token?>">
 *
 * @author Mikhail Osher, https://github.com/miraage
 * @version 1.0
 */
class CsrfGuard extends Slim_Middleware {
    /**
     * Request key
     *
     * @var string
     */
    protected $key;

    /**
     * Constructor
     *
     * @param string $key Request key
     */
    public function __construct( $key = 'csrf_token' ) {
        // Validate key (i won't use htmlspecialchars)
        if ( !is_string($key) || empty($key) || preg_match('/[^a-zA-Z0-9\-\_]/', $key) ) {
            throw new OutOfBoundsException('Invalid key' . $key);
        }

        $this->key = $key;
    }

    /**
     * Call middleware
     */
    public function call() {
        // Attach as hook
        $this->app->hook('slim.before', array($this, 'check'));

        // Call next middleware
        $this->next->call();
    }

    /**
     * Check token
     */
    public function check() {
        // Create token
        $env = $this->app->environment();

        if ( "" === session_id() ){
            if ( ! isset( $_SESSION[ $this->key ] ) ){
                $_SESSION[ $this->key ] = sha1( serialize( $_SERVER ) . rand( 0, 0xffffffff ) );
            }
        } else {
            throw new Exception( "Session are required to use CSRF Guard" );
        }
        $token = $_SESSION[ $this -> key ];

        // Validate
        if ( in_array($this->app->request()->getMethod(), array('POST', 'PUT', 'DELETE')) ) {
            $usertoken = $this->app->request()->post($this->key);
            if ( $token !== $usertoken ) {
                $this->app->halt(400, 'Missing token');
            }
        }

        // Assign to view
        $this->app->view()->appendData(array(
            'csrf_key' => $this->key,
            'csrf_token' => $token,
        ));
    }
}

?>
