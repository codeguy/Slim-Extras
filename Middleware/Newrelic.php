<?php
/**
 * Newrelic integration
 *
 * Use this middleware with your Slim Framework application
 * to track your transactions with newrelic.com
 *
 * USAGE
 *
 * $app = new \Slim\Slim();
 * $app->add(new \Slim\Extras\Middleware\Newrelic());
 *
 */
namespace Slim\Extras\Middleware;

class Newrelic extends \Slim\Middleware
{
    /**
     * Newrelic extension status
     *
     * @var boolean
     */
    private $_extensionLoaded = null;

    /**
     * Newrelic variable
     *
     * @var string
     */
    private $_transaction = null;
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Call middleware.
     *
     * @return void
     */
    public function call()
    {
        // Attach hooks.
        $this->app->hook("slim.after.router", array($this, "setTransactionName"));
        $this->app->hook("slim.after", array($this, "nameTransaction"));

        // Call next middleware.
        $this->next->call();
    }

    /**
     * Newrelic transaction name API
     *
     * @return void
     */
    public function nameTransaction()
    {
        if ($this->extensionLoaded()) {
            newrelic_name_transaction($this->getTransactionName());
        }
    }

    /**
     * Set transaction name
     *
     * @return void
     */
    public function setTransactionName()
    {
        // Set the transaction name based on the path info
        // TODO: Handle route parameters
        $this->_transaction = $this->app->request()->getPathInfo();
    }

    /**
     * Get transaction name
     *
     * @return void
     */
    public function getTransactionName()
    {
        if (null === $this->_transaction) {
            $this->setTransactionName();
        }

        return $this->_transaction;
    }

    /**
     * Check if Newrelic extension is loaded
     *
     * @return boolean
     */
    public function extensionLoaded()
    {
        // Check if the extension is loaded and store the result
        if (null === $this->_extensionLoaded) {
            $this->_extensionLoaded = extension_loaded("newrelic");
        }

        // Return
        return $this->_extensionLoaded;
    }

}
