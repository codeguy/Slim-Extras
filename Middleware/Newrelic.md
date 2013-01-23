## Newrelic

This will provde basic transaction tracking for newrelic.com users

### How to use

    use \Slim\Slim;
    use \Slim\Extras\Middleware\Newrelic;

    $app = new Slim();
    $app->add(new Newrelic());
