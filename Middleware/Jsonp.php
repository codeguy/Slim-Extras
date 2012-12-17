<?php
/**
 * JSONP Middleware Class for the Slim Framework
 *
 * Simple class to wrap the response of the application in an JSONP callback function.
 * The class is triggered when a get parameter of callback is found	
 * 
 * @author  Tom van Oorschot <tomvanoorschot@gmail.com>
 * @since  17-12-2012
 */
class JSONPMiddleware extends \Slim\Middleware
{
    public function call()
    {
    	$callback = $this->app->request()->get('callback');

    	//Fetch the body first
		$this->next->call();

		//If the JSONP callback parameter is set then wrap the response body in the original
		//callback string.
    	if(!empty($callback)){
    		//The response becomes a javascript response
    		$this->app->contentType('application/javascript');

    		$jsonp_response = htmlspecialchars($callback) . "(" .$this->app->response()->body() . ")";
    		$this->app->response()->body($jsonp_response);
    	}
   	}
}