<?php
/**
 * API Middleware Class for the Slim Framework
 *
 * @author  Montana Flynn <montana@montanaflynn.me>
 * @since  3/10/13
 *
 * Simple class to make building API's easier
 *
 * Usage
 * ====
 * 
 * $api = new \Slim\slim();
 * $api->add(new \Slim\Extras\Middleware\API());
 * 
 */

namespace Slim\Extras\Middleware;

class API extends \Slim\Middleware
{
  public function call()
  {
    
    // Just to make things easy, we can avoid the 404 page and override with 
    // helpful error messages.  May extend later to find all registered endpoints
    $app = $this->app;
    
    // Change to json
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    
    // No Endpoint Specified?
    $app->get('/', function()  use ($app) {
    	$app->halt(400, json_encode(array('error'=>'You must specify an endpoint!')));
    });

    // Cannot Find Endpoint?
    $app->get('/:method', function($method) use ($app) {
    	$app->halt(400, json_encode(array('error'=>'There is no endpoint named '.$method.'!')));
    })->conditions(array('method' => '.+'));

    // Output jsonp if user sets a callback parameter
    // Tom van Oorschot <tomvanoorschot@gmail.com>
    $request = $app->request();
    $callback = $request->params('callback');
    
    // Get the next response so we can wrap it
    $this->next->call();
  
    // Do the damn thing
    if(!empty($callback)){
      $this->app->contentType('application/javascript');
      $jsonp_response = htmlspecialchars($callback) . "(" .$this->app->response()->body() . ")";
      $this->app->response()->body($jsonp_response);
    }
  }
}